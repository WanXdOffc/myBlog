<?php

use App\Http\Controllers\FrontPostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public Blog Routes ──────────────────────────────────────────────────────
Route::get('/', [FrontPostController::class, 'index'])->name('home');
Route::get('/posts/{slug}', [FrontPostController::class, 'show'])->name('posts.show');
Route::get('/api/search', [FrontPostController::class, 'liveSearch'])->name('posts.live-search');
Route::feeds();

// Redirect /dashboard to home (or dashboard route alias)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Authenticated User Routes ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/posts/{slug}/comments', [FrontPostController::class, 'storeComment'])
        ->middleware('throttle:2,1')
        ->name('posts.comments.store');
    Route::post('/posts/{slug}/like', [FrontPostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{slug}/bookmark', [FrontPostController::class, 'toggleBookmark'])->name('posts.bookmark');
    Route::get('/bookmarks', [FrontPostController::class, 'bookmarks'])->name('posts.bookmarks');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
