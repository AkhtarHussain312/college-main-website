<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $events = Event::where('is_published', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($event) => array_merge($event->toArray(), [
                'image_url' => $event->image_path ? asset('storage/'.$event->image_path) : null,
            ]));

        return response()->json(['data' => $events]);
    }

    public function show(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return response()->json(['data' => array_merge($event->toArray(), [
            'image_url' => $event->image_path ? asset('storage/'.$event->image_path) : null,
        ])]);
    }
}
