<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\AdmissionMilestone;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Inquiry;
use App\Models\Program;
use App\Models\ProgramFee;
use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentManagerController extends Controller
{
    public function __construct(private ImageUploadService $images)
    {
    }

    private function model(string $type): Model
    {
        return match ($type) {
            'programs' => new Program,
            'about-sections' => new AboutSection,
            'fees' => new ProgramFee,
            'faculty' => new Faculty,
            'events' => new Event,
            'milestones' => new AdmissionMilestone,
            'inquiries' => new Inquiry,
            default => abort(404),
        };
    }

    private function rules(string $type): array
    {
        return match ($type) {
            'programs' => [
                'name' => 'required|max:150',
                'slug' => 'required|max:160',
                'duration' => 'required|max:80',
                'description' => 'required|max:2000',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'sort_order' => 'integer',
                'is_active' => 'boolean',
            ],
            'about-sections' => [
                'title' => 'required|max:180',
                'body' => 'required|string|max:50000',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'image_position' => 'required|in:left,right',
                'sort_order' => 'integer',
                'is_active' => 'boolean',
            ],
            'fees' => [
                'program_id' => 'required|exists:programs,id',
                'fee_type' => 'required|string|max:150',
                'amount' => 'required|numeric|min:0|max:9999999999',
                'currency' => 'required|string|size:3',
                'billing_basis' => 'required|string|max:60',
                'notes' => 'nullable|string|max:2000',
                'sort_order' => 'integer',
                'is_active' => 'boolean',
            ],
            'faculty' => [
                'name' => 'required|max:150',
                'title' => 'required|max:150',
                'qualification' => 'nullable|max:150',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'sort_order' => 'integer',
                'is_active' => 'boolean',
            ],
            'events' => [
                'title' => 'required|max:180',
                'slug' => 'required|max:180',
                'excerpt' => 'required|max:2000',
                'body' => 'nullable|string|max:50000',
                'event_date' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'is_published' => 'boolean',
            ],
            'milestones' => [
                'title' => 'required|max:150',
                'icon' => 'nullable|max:100',
                'sort_order' => 'integer',
            ],
            'inquiries' => [
                'status' => 'required|in:new,contacted,enrolled,closed',
            ],
        };
    }

    private function serialize(Model $item): array
    {
        $data = $item->toArray();

        if ($item instanceof ProgramFee) {
            $item->loadMissing('program:id,name');
            $data['name'] = $item->fee_type;
            $data['program'] = $item->program?->name;
        }

        if (array_key_exists('image_path', $data)) {
            $data['image_url'] = $data['image_path'] ? asset('storage/'.$data['image_path']) : null;
        }

        return $data;
    }

    public function index(string $type): JsonResponse
    {
        $query = $this->model($type)->newQuery();

        if ($type === 'fees') {
            $query->with('program:id,name')->orderBy('program_id')->orderBy('sort_order');
        } elseif (in_array($type, ['about-sections', 'programs', 'faculty', 'milestones'])) {
            $query->orderBy('sort_order')->orderByDesc('id');
        } else {
            $query->latest();
        }

        $page = $query->paginate(20);
        $page->setCollection($page->getCollection()->map(fn ($x) => $this->serialize($x)));

        return response()->json([
            'data' => $page,
            'options' => $type === 'fees' ? ['programs' => Program::orderBy('name')->get(['id', 'name'])] : null,
        ]);
    }

    public function store(Request $r, string $type): JsonResponse
    {
        $data = $r->validate($this->rules($type));
        unset($data['image']);

        $item = $this->model($type)->newQuery()->create($data);

        if ($r->hasFile('image')) {
            $item->update(['image_path' => $this->images->replace($r->file('image'), null, $type)]);
        }

        return response()->json(['data' => $this->serialize($item->fresh())], 201);
    }

    public function update(Request $r, string $type, int $id): JsonResponse
    {
        $item = $this->model($type)->newQuery()->findOrFail($id);
        $data = $r->validate($this->rules($type));
        unset($data['image']);

        $item->update($data);

        if ($r->hasFile('image') && in_array($type, ['programs', 'about-sections', 'faculty', 'events'])) {
            $item->update(['image_path' => $this->images->replace($r->file('image'), $item->image_path, $type)]);
        }

        return response()->json(['data' => $this->serialize($item->fresh())]);
    }

    public function destroy(string $type, int $id): JsonResponse
    {
        abort_if($type === 'inquiries', 405);

        $item = $this->model($type)->newQuery()->findOrFail($id);
        $this->images->delete($item->image_path ?? null);
        $item->delete();

        return response()->json([], 204);
    }
}
