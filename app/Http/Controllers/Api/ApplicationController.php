<?php

namespace App\Http\Controllers\Api;

use App\Actions\SubmitApplicationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{
    /**
     * Store incoming online application via the unified action.
     */
    public function store(StoreApplicationRequest $request, SubmitApplicationAction $action): JsonResponse
    {
        $application = $action->execute($request);

        return response()->json([
            'message' => 'Application submitted successfully.',
            'data' => [
                'reference' => $application->reference,
                'status' => $application->status,
            ],
        ], 201);
    }

    /**
     * Track application status by reference or CNIC/email.
     */
    public function track(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = trim((string) $request->input('query', $request->input('reference', '')));

        if (empty($query)) {
            return response()->json([
                'message' => 'Please provide your application reference number or CNIC.',
            ], 422);
        }

        $application = \App\Models\Application::with('program')
            ->where(function ($q) use ($query) {
                $q->where('reference', $query)
                  ->orWhere('national_id', $query)
                  ->orWhere('email', $query);
            })
            ->latest()
            ->first();

        if (! $application) {
            return response()->json([
                'message' => 'No application record found for "'.$query.'". Please verify your reference number or CNIC.',
            ], 404);
        }

        $statusSteps = [
            'submitted' => [
                'step' => 1,
                'label' => 'Application Received',
                'description' => 'Your application and scanned documents have been successfully registered into our admissions database.',
                'badge' => 'primary',
            ],
            'under_review' => [
                'step' => 2,
                'label' => 'Under Academic Review',
                'description' => 'The admissions evaluation committee is verifying your academic transcripts, eligibility criteria, and prerequisites.',
                'badge' => 'warning',
            ],
            'shortlisted' => [
                'step' => 3,
                'label' => 'Shortlisted for Interview & Test',
                'description' => 'Congratulations! Your profile has been shortlisted. Please review interview dates and bring original academic credentials.',
                'badge' => 'info',
            ],
            'accepted' => [
                'step' => 4,
                'label' => 'Admission Granted',
                'description' => 'Official admission offer granted. Please deposit admission dues and complete document verification to secure your seat.',
                'badge' => 'success',
            ],
            'rejected' => [
                'step' => 2,
                'label' => 'Application Not Accepted',
                'description' => 'Regrettably, your application did not meet the required merit cutoff or eligibility criteria for this academic session.',
                'badge' => 'danger',
            ],
        ];

        $currentStatusInfo = $statusSteps[$application->status] ?? $statusSteps['submitted'];

        $maskedName = $application->first_name . ' ' . (strlen($application->last_name) > 1 ? substr($application->last_name, 0, 1) . '***' : $application->last_name);
        $maskedCnic = $application->national_id ? substr($application->national_id, 0, 5) . '*****' . substr($application->national_id, -2) : null;

        return response()->json([
            'data' => [
                'reference' => $application->reference,
                'status' => $application->status,
                'status_label' => $currentStatusInfo['label'],
                'status_description' => $currentStatusInfo['description'],
                'status_badge' => $currentStatusInfo['badge'],
                'current_step' => $currentStatusInfo['step'],
                'applicant_name' => $maskedName,
                'masked_cnic' => $maskedCnic,
                'program_name' => $application->program?->name ?? 'Nursing Program',
                'program_duration' => $application->program?->duration,
                'submitted_at' => $application->submitted_at?->format('M d, Y h:i A') ?? $application->created_at->format('M d, Y'),
                'admin_notes' => $application->admin_notes,
            ],
        ]);
    }
}
