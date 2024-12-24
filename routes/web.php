<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'welcome')->name('welcome');
    Route::get('/about', 'about')->name('about');
    Route::get('/features', 'features')->name('features');

    // Optional: Rate Limiting für Kontaktformular
    Route::get('/contact', 'contact')
        ->name('contact')
        ->middleware(['throttle:60,1']);

    // Optional: Caching für statische Seiten
    Route::get('/privacy', 'privacy')
        ->name('privacy')
        ->middleware(['cache.headers:private;max_age=0;no_cache;no_store;must_revalidate']);

    Route::get('/terms', 'terms')
        ->name('terms')
        ->middleware(['cache.headers:public;max_age=3600']);
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'title' => 'Dashboard',
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
//
