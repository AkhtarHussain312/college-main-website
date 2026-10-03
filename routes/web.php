<?php
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentManagerController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;
Route::prefix('api/admin')->group(function(){
 Route::post('/login',[AuthController::class,'login']);
 Route::middleware(['auth','admin'])->group(function(){
  Route::get('/me',[AuthController::class,'me']);Route::post('/logout',[AuthController::class,'logout']);
  Route::get('/site-content',[SiteContentController::class,'index']);Route::put('/site-content/{siteContent}',[SiteContentController::class,'update']);Route::post('/site-content/{siteContent}',[SiteContentController::class,'update']);
  Route::get('/applications',[ApplicationController::class,'index']);Route::put('/applications/{application}',[ApplicationController::class,'update']);Route::get('/applications/{application}/print',[ApplicationController::class,'printView']);Route::get('/applications/{application}/documents/{type}',[ApplicationController::class,'document'])->whereIn('type',['transcript','identity']);
  Route::get('/messages',[ContactMessageController::class,'index']);Route::put('/messages/{message}',[ContactMessageController::class,'update']);
  Route::get('/{type}',[ContentManagerController::class,'index'])->whereIn('type',['programs','about-sections','fees','faculty','events','milestones','inquiries']);
  Route::post('/{type}',[ContentManagerController::class,'store'])->whereIn('type',['programs','about-sections','fees','faculty','events','milestones']);
  Route::put('/{type}/{id}',[ContentManagerController::class,'update'])->whereIn('type',['programs','about-sections','fees','faculty','events','milestones','inquiries']);Route::post('/{type}/{id}',[ContentManagerController::class,'update'])->whereIn('type',['programs','about-sections','fees','faculty','events','milestones','inquiries']);
  Route::delete('/{type}/{id}',[ContentManagerController::class,'destroy'])->whereIn('type',['programs','about-sections','fees','faculty','events','milestones']);
 });
});
Route::get('/', [PublicPageController::class, 'home']);
Route::get('/about', [PublicPageController::class, 'about']);
Route::get('/programs', [PublicPageController::class, 'programs']);
Route::get('/faculty', [PublicPageController::class, 'faculty']);
Route::get('/news-events', [PublicPageController::class, 'news']);
Route::get('/news-events/{slug}', [PublicPageController::class, 'newsShow']);
Route::get('/apply', [PublicPageController::class, 'apply']);
Route::post('/apply', [PublicPageController::class, 'applyStore'])->middleware('throttle:5,1');
Route::get('/contact', [PublicPageController::class, 'contact']);
Route::post('/contact', [PublicPageController::class, 'contactStore'])->middleware('throttle:5,1');
Route::get('/sitemap.xml', [PublicPageController::class, 'sitemap']);
Route::get('/{any?}',function(){try{$site=\Illuminate\Support\Facades\Schema::hasTable('site_contents')?\App\Models\SiteContent::pluck('value','key'):collect();}catch(\Throwable){$site=collect();}return view('welcome',['collegeName'=>$site->get('college_name','Dir College Of Nursing & Allied Health Science'),'metaDescription'=>$site->get('footer_description','Professional nursing and allied health sciences education for compassionate healthcare careers.')]);})->where('any','^(?!api).*$');

