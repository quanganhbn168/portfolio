<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Http\Controllers\Frontend\ServicesController;
use App\Support\Branding\FaviconService;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', function (FaviconService $favicons) {
    return response()->file($favicons->primaryPath(), [
        'Content-Type' => $favicons->primaryMimeType(),
        'Cache-Control' => 'public, max-age=604800',
    ]);
});

Route::post('/language/{locale}', LanguageController::class)->name('language.switch');

Route::group([], function (): void {
    Route::get('/', [ProjectController::class, 'index'])->name('home');
    Route::redirect('/projects', '/')->name('projects.index');
    Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
    Route::get('/services', ServicesController::class)->name('services.index');
    Route::get('/about', AboutController::class)->name('about');
    // Liên hệ nằm ở cuối trang chủ (không có trang riêng); giữ đường dẫn cũ để link cũ vẫn hoạt động.
    Route::redirect('/contact', '/#lien-he')->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:frontend-contact')->name('contact.store');
});

Route::get('/{slug}', PublicSlugController::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');
