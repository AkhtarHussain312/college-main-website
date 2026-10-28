<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Models\SiteContent;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'program_id' => 'nullable|integer|exists:programs,id',
            'year' => 'nullable|integer|min:2000|max:'.(date('Y') + 1),
        ]);

        $query = Application::with('program:id,name')
            ->when($filters['program_id'] ?? null, fn ($q, $id) => $q->where('program_id', $id))
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->whereYear('submitted_at', $year));

        $page = $query->latest('submitted_at')->paginate(20)->withQueryString();
        $page->setCollection($page->getCollection()->map(fn ($x) => array_merge($x->toArray(), [
            'name' => $x->first_name.' '.$x->last_name,
            'program' => $x->program?->name,
        ])));

        $years = Application::whereNotNull('submitted_at')
            ->pluck('submitted_at')
            ->map(fn ($date) => (int) ($date instanceof CarbonInterface ? $date->year : date('Y', strtotime((string) $date))))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return response()->json([
            'data' => $page,
            'filters' => [
                'programs' => Program::orderBy('name')->get(['id', 'name']),
                'years' => $years,
            ],
        ]);
    }

    public function update(Request $request, Application $application): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,shortlisted,accepted,rejected',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $application->update($validated);

        return response()->json(['data' => $application]);
    }

    public function printView(Application $application): View
    {
        $application->load('program:id,name,duration');
        $collegeName = SiteContent::where('key', 'college_name')->value('value') ?: 'College of Nursing';

        return view('admin.application-print', compact('application', 'collegeName'));
    }

    public function document(Application $application, string $type): StreamedResponse
    {
        $path = match ($type) {
            'transcript' => $application->transcript_path,
            'identity' => $application->identity_document_path,
            default => abort(404),
        };

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download(
            $path,
            $application->reference.'-'.$type.'.'.pathinfo($path, PATHINFO_EXTENSION)
        );
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'program_id' => 'nullable|integer|exists:programs,id',
            'year' => 'nullable|integer|min:2000|max:'.(date('Y') + 1),
            'status' => 'nullable|string|in:submitted,under_review,shortlisted,accepted,rejected',
        ]);

        $query = Application::with('program:id,name')
            ->when($filters['program_id'] ?? null, fn ($q, $id) => $q->where('program_id', $id))
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->whereYear('submitted_at', $year))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest('submitted_at');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dcn-applications-'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel on Windows
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Reference',
                'Status',
                'Program',
                'First Name',
                'Last Name',
                'CNIC / National ID',
                'Gender',
                'Date of Birth',
                'Phone',
                'Email',
                'City',
                'Address',
                'Highest Qualification',
                'Institution',
                'Graduation Year',
                'Grade / Percentage',
                'Admin Notes',
                'Submitted At',
            ]);

            $query->chunk(100, function ($apps) use ($handle) {
                foreach ($apps as $app) {
                    fputcsv($handle, [
                        $app->reference,
                        str_replace('_', ' ', strtoupper($app->status)),
                        $app->program?->name ?? 'N/A',
                        $app->first_name,
                        $app->last_name,
                        $app->national_id,
                        ucfirst($app->gender ?? ''),
                        $app->date_of_birth?->format('Y-m-d') ?? '',
                        $app->phone,
                        $app->email,
                        $app->city,
                        $app->address,
                        $app->highest_qualification,
                        $app->institution,
                        $app->graduation_year,
                        $app->grade_percentage,
                        $app->admin_notes,
                        $app->submitted_at?->format('Y-m-d H:i') ?? $app->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function destroy(Application $application): JsonResponse
    {
        if ($application->transcript_path) {
            Storage::disk('local')->delete($application->transcript_path);
        }
        if ($application->identity_document_path) {
            Storage::disk('local')->delete($application->identity_document_path);
        }

        $application->delete();

        return response()->json([], 204);
    }
}
