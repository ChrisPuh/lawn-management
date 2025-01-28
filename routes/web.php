<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Lawn\LawnCreate;
use App\Livewire\Lawn\LawnEdit;
use Illuminate\Support\Facades\Route;

// Public routes - no consent needed
Route::controller(PageController::class)->group(function (): void {
    Route::get('/', 'welcome')->name('welcome');
    Route::get('/about', 'about')->name('about');
    Route::get('/features', 'features')->name('features');
    Route::get('/contact', 'contact')
        ->name('contact')
        ->middleware(['throttle:60,1']);

    // Legal pages
    Route::get('/privacy', 'privacy')
        ->name('privacy')
        ->middleware(['cache.headers:private;max_age=0;no_cache;no_store;must_revalidate']);
    Route::get('/terms', 'terms')
        ->name('terms')
        ->middleware(['cache.headers:public;max_age=3600']);
    Route::get('/cookie-policy', 'cookiePolicy')
        ->name('cookie-policy')
        ->middleware(['cache.headers:private;max_age=0;no_cache;no_store;must_revalidate']);
});

// Protected routes requiring auth AND cookie consent
Route::middleware(['auth', 'verified', 'cookie.consent'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'title' => 'Dashboard',
        ]);
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lawn management routes
    Route::as('lawn.')->prefix('lawn')->group(function (): void {
        Route::get('/', App\Http\Controllers\Lawn\LawnIndexController::class)
            ->middleware('can:viewAny,App\Models\Lawn')
            ->name('index');

        Route::get('/create', LawnCreate::class)
            ->middleware('can:create,App\Models\Lawn')
            ->name('create');

        Route::get('/{lawn}', App\Http\Controllers\Lawn\LawnShowController::class)
            ->middleware('can:view,lawn')
            ->name('show');

        Route::get('/{lawn}/edit', LawnEdit::class)->name('edit');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/feedback', function () {
        return view('feedback.create', [
            'title' => 'Feedback'
        ]);
    })->name('feedback');
});

// Auth routes - no consent needed
require __DIR__.'/auth.php';
