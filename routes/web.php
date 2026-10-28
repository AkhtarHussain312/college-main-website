<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentManagerController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/site-content', [SiteContentController::class, 'index']);
        Route::put('/site-content/{siteContent}', [SiteContentController::class, 'update']);
        Route::post('/site-content/{siteContent}', [SiteContentController::class, 'update']);
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::get('/applications/export', [ApplicationController::class, 'export']);
        Route::put('/applications/{application}', [ApplicationController::class, 'update']);
        Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']);
        Route::get('/applications/{application}/print', [ApplicationController::class, 'printView']);
        Route::get('/applications/{application}/documents/{type}', [ApplicationController::class, 'document'])->whereIn('type', ['transcript', 'identity']);
        Route::get('/messages', [ContactMessageController::class, 'index']);
        Route::put('/messages/{message}', [ContactMessageController::class, 'update']);
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy']);
        Route::get('/{type}', [ContentManagerController::class, 'index'])->whereIn('type', ['programs', 'about-sections', 'fees', 'faculty', 'events', 'milestones', 'inquiries', 'merit-lists']);
        Route::post('/{type}', [ContentManagerController::class, 'store'])->whereIn('type', ['programs', 'about-sections', 'fees', 'faculty', 'events', 'milestones', 'merit-lists']);
        Route::put('/{type}/{id}', [ContentManagerController::class, 'update'])->whereIn('type', ['programs', 'about-sections', 'fees', 'faculty', 'events', 'milestones', 'inquiries', 'merit-lists']);
        Route::post('/{type}/{id}', [ContentManagerController::class, 'update'])->whereIn('type', ['programs', 'about-sections', 'fees', 'faculty', 'events', 'milestones', 'inquiries', 'merit-lists']);
        Route::delete('/{type}/{id}', [ContentManagerController::class, 'destroy'])->whereIn('type', ['programs', 'about-sections', 'fees', 'faculty', 'events', 'milestones', 'merit-lists']);
    });
});
Route::get('/', [PublicPageController::class, 'home']);
Route::get('/about', [PublicPageController::class, 'about']);
Route::get('/programs', [PublicPageController::class, 'programs']);
Route::get('/faculty', [PublicPageController::class, 'faculty']);
Route::get('/admissions', [PublicPageController::class, 'admissions']);
Route::get('/campus-life', [PublicPageController::class, 'campusLife']);
Route::get('/merit-lists', [PublicPageController::class, 'meritLists']);
Route::get('/track', [PublicPageController::class, 'trackApplication']);
Route::get('/news-events', [PublicPageController::class, 'news']);
Route::get('/news-events/{slug}', [PublicPageController::class, 'newsShow']);
Route::get('/apply', [PublicPageController::class, 'apply']);
Route::post('/apply', [PublicPageController::class, 'applyStore'])->middleware('throttle:5,1');
Route::get('/contact', [PublicPageController::class, 'contact']);
Route::post('/contact', [PublicPageController::class, 'contactStore'])->middleware('throttle:5,1');
Route::get('/sitemap.xml', [PublicPageController::class, 'sitemap']);
Route::get('/{any?}', function () {
    try {
        $site = \Illuminate\Support\Facades\Schema::hasTable('site_contents') ? \App\Models\SiteContent::pluck('value', 'key') : collect();
    } catch (\Throwable) {
        $site = collect();
    }

    return view('welcome', ['collegeName' => $site->get('college_name', 'Dir College Of Nursing & Allied Health Science'), 'metaDescription' => $site->get('footer_description', 'Professional nursing and allied health sciences education for compassionate healthcare careers.')]);
})->where('any', '^(?!api).*$');
