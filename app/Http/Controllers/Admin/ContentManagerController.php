<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\AdmissionMilestone;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Inquiry;
use App\Models\MeritList;
use App\Models\Program;
use App\Models\ProgramFee;
use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentManagerController extends Controller
{
    public function __construct(private ImageUploadService $images) {}

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
            'merit-lists' => new MeritList,
            default => abort(404),
        };
    }

    private function rules(string $type, ?int $id = null): array
    {
        return match ($type) {
            'merit-lists' => [
                'title' => 'required|max:200',
                'category' => 'required|in:merit_list,prospectus,roll_number_slip,form,academic_calendar',
                'session_year' => 'nullable|string|max:50',
                'program_id' => 'nullable|exists:programs,id',
                'file' => 'nullable|file|max:25600',
                'description' => 'nullable|string|max:2000',
                'sort_order' => 'integer',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
            ],
            'programs' => [
                'name' => 'required|max:150',
                'slug' => ['required', 'max:160', $id ? Rule::unique('programs', 'slug')->ignore($id) : Rule::unique('programs', 'slug')],
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
                'slug' => ['required', 'max:180', $id ? Rule::unique('events', 'slug')->ignore($id) : Rule::unique('events', 'slug')],
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

        if ($item instanceof MeritList) {
            $item->loadMissing('program:id,name');
            $data['program'] = $item->program?->name;
            $data['file_url'] = $item->file_path ? asset('storage/'.$item->file_path) : null;
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
        } elseif ($type === 'merit-lists') {
            $query->with('program:id,name')->orderBy('sort_order')->orderByDesc('published_at');
        } elseif (in_array($type, ['about-sections', 'programs', 'faculty', 'milestones'])) {
            $query->orderBy('sort_order')->orderByDesc('id');
        } else {
            $query->latest();
        }

        $page = $query->paginate(20);
        $page->setCollection($page->getCollection()->map(fn ($x) => $this->serialize($x)));

        return response()->json([
            'data' => $page,
            'options' => in_array($type, ['fees', 'merit-lists', 'programs']) ? ['programs' => Program::orderBy('name')->get(['id', 'name'])] : null,
        ]);
    }

    public function store(Request $r, string $type): JsonResponse
    {
        if ($type === 'merit-lists' && in_array($r->input('program_id'), ['', 'null', '0', null], true)) {
            $r->merge(['program_id' => null]);
        }

        $data = $r->validate($this->rules($type));
        unset($data['image'], $data['file']);

        if ($type === 'merit-lists') {
            if (! isset($data['file_path'])) {
                $data['file_path'] = 'documents/dcn-official-prospectus-2026.pdf';
            }
            if (! isset($data['program_id']) || $data['program_id'] === '') {
                $data['program_id'] = null;
            }
        }

        $item = $this->model($type)->newQuery()->create($data);

        if ($r->hasFile('image')) {
            $item->update(['image_path' => $this->images->replace($r->file('image'), null, $type)]);
        }

        if ($r->hasFile('file') && $type === 'merit-lists') {
            $file = $r->file('file');
            $path = $file->store('documents', 'public');
            $size = round($file->getSize() / 1024, 1);
            $sizeFormatted = $size >= 1024 ? round($size / 1024, 2).' MB' : round($size).' KB';
            $item->update(['file_path' => $path, 'file_size' => $sizeFormatted]);
        }

        return response()->json(['data' => $this->serialize($item->fresh())], 201);
    }

    public function update(Request $r, string $type, int $id): JsonResponse
    {
        $item = $this->model($type)->newQuery()->findOrFail($id);

        if ($type === 'merit-lists' && in_array($r->input('program_id'), ['', 'null', '0', null], true)) {
            $r->merge(['program_id' => null]);
        }

        $data = $r->validate($this->rules($type, $id));
        unset($data['image'], $data['file']);

        if ($type === 'merit-lists' && (! isset($data['program_id']) || $data['program_id'] === '')) {
            $data['program_id'] = null;
        }

        $item->update($data);

        if ($r->hasFile('image') && in_array($type, ['programs', 'about-sections', 'faculty', 'events'])) {
            $item->update(['image_path' => $this->images->replace($r->file('image'), $item->image_path, $type)]);
        }

        if ($r->hasFile('file') && $type === 'merit-lists') {
            $file = $r->file('file');
            $path = $file->store('documents', 'public');
            $size = round($file->getSize() / 1024, 1);
            $sizeFormatted = $size >= 1024 ? round($size / 1024, 2).' MB' : round($size).' KB';
            $item->update(['file_path' => $path, 'file_size' => $sizeFormatted]);
        }

        return response()->json(['data' => $this->serialize($item->fresh())]);
    }

    public function destroy(string $type, int $id): JsonResponse
    {
        abort_if($type === 'inquiries', 405);

        $item = $this->model($type)->newQuery()->findOrFail($id);
        $this->images->delete($item->image_path ?? null);

        if ($item instanceof Program) {
            $item->fees()->delete();
            \App\Models\Application::where('program_id', $item->id)->update(['program_id' => null]);
            \App\Models\MeritList::where('program_id', $item->id)->update(['program_id' => null]);
        }

        $item->delete();

        return response()->json([], 204);
    }
}
