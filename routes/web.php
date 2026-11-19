<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Models\Comment;
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
    Route::get('/about', AboutController::class)->name('about');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:frontend-contact')->name('contact.store');
    Route::post('/comments/{type}/{id}', [CommentController::class, 'store'])
        ->whereIn('type', Comment::COMMENTABLE_TYPES)
        ->whereNumber('id')
        ->middleware('throttle:frontend-comment')
        ->name('comments.store');
});

Route::get('/{slug}', PublicSlugController::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');
