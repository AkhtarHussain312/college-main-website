<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Models\AboutSection;
use App\Models\AdmissionMilestone;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\SiteContent;
use Illuminate\Http\JsonResponse;

class CollegeContentController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $site = SiteContent::get()->mapWithKeys(fn ($x) => [
            $x->key => $x->type === 'image' && $x->value ? asset('storage/'.$x->value) : $x->value,
        ]);

        $withUrl = fn ($x) => array_merge($x->toArray(), [
            'image_url' => $x->image_path ? asset('storage/'.$x->image_path) : null,
        ]);

        return response()->json([
            'data' => [
                'site' => $site,
                'programs' => ProgramResource::collection(
                    Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                ),
                'about_sections' => AboutSection::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(['id', 'title', 'body', 'image_path', 'image_position', 'sort_order'])
                    ->map($withUrl),
                'faculty' => Faculty::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(['id', 'name', 'title', 'qualification', 'image_path'])
                    ->map($withUrl),
                'events' => Event::where('is_published', true)
                    ->orderByDesc('event_date')
                    ->limit(3)
                    ->get(['id', 'title', 'slug', 'excerpt', 'event_date', 'image_path'])
                    ->map($withUrl),
                'milestones' => AdmissionMilestone::orderBy('sort_order')->get(['id', 'title', 'icon']),
            ],
        ]);
    }
}
