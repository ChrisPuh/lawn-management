<?php

declare(strict_types=1);

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Lawn\LawnCreate;
use App\Livewire\Lawn\LawnEdit;
use App\Livewire\Lawn\LawnIndex;
use App\Livewire\Lawn\LawnShow;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::controller(PageController::class)->group(function (): void {
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

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
//

Route::middleware('auth')->as('lawn.')->prefix('lawn')->group(function (): void {
    Route::get('/', LawnIndex::class)->name('index');
    Route::get('/create', LawnCreate::class)->name('create');
    Route::get('/{lawn}', LawnShow::class)->name('show');
    Route::get('/{lawn}/edit', LawnEdit::class)->name('edit');
});
