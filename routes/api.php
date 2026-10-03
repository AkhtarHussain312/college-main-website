<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\CollegeContentController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/college-content', CollegeContentController::class);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{slug}', [EventController::class, 'show']);
Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:10,1');
Route::post('/applications',[ApplicationController::class,'store'])->middleware('throttle:5,1');
Route::post('/contact-messages',[ContactMessageController::class,'store'])->middleware('throttle:5,1');
Route::post('/chat', [ChatbotController::class, 'chat'])->middleware('throttle:30,1');
