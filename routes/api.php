<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\CollegeContentController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\InquiryController;
use Illuminate\Support\Facades\Route;

Route::get('/college-content', CollegeContentController::class);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{slug}', [EventController::class, 'show']);
Route::get('/applications/track', [ApplicationController::class, 'track'])->middleware('throttle:20,1');
Route::get('/merit-lists', function () {
    $lists = \App\Models\MeritList::with('program:id,name')
        ->where('is_published', true)
        ->orderBy('sort_order')
        ->orderByDesc('published_at')
        ->get()
        ->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'category' => $item->category,
            'session_year' => $item->session_year,
            'program' => $item->program?->name,
            'file_url' => asset('storage/'.$item->file_path),
            'file_size' => $item->file_size,
            'description' => $item->description,
            'published_at' => $item->published_at?->format('M d, Y') ?? $item->created_at->format('M d, Y'),
        ]);

    return response()->json(['data' => $lists]);
});
Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:10,1');
Route::post('/applications', [ApplicationController::class, 'store'])->middleware('throttle:5,1');
Route::post('/contact-messages', [ContactMessageController::class, 'store'])->middleware('throttle:5,1');
Route::post('/chat', [ChatbotController::class, 'chat'])->middleware('throttle:30,1');
