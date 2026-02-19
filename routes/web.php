<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return redirect()->route('campaigns.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Campaign routes
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{slug}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{slug}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{slug}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    // Character routes (within a campaign)
    Route::get('/campaigns/{campaignSlug}/characters', [NodeController::class, 'charactersIndex'])
        ->name('campaigns.characters.index');
    Route::get('/campaigns/{campaignSlug}/characters/create', [NodeController::class, 'charactersCreate'])
        ->name('campaigns.characters.create');
    Route::post('/campaigns/{campaignSlug}/characters', [NodeController::class, 'charactersStore'])
        ->name('campaigns.characters.store');
    Route::get('/campaigns/{campaignSlug}/characters/{nodeSlug}', [NodeController::class, 'charactersShow'])
        ->name('campaigns.characters.show');
    Route::get('/campaigns/{campaignSlug}/characters/{nodeSlug}/edit', [NodeController::class, 'charactersEdit'])
        ->name('campaigns.characters.edit');
    Route::put('/campaigns/{campaignSlug}/characters/{nodeSlug}', [NodeController::class, 'charactersUpdate'])
        ->name('campaigns.characters.update');
    Route::delete('/campaigns/{campaignSlug}/characters/{nodeSlug}', [NodeController::class, 'charactersDestroy'])
        ->name('campaigns.characters.destroy');

    // Place routes (within a campaign)
    Route::get('/campaigns/{campaignSlug}/places', [NodeController::class, 'placesIndex'])
        ->name('campaigns.places.index');
    Route::get('/campaigns/{campaignSlug}/places/create', [NodeController::class, 'placesCreate'])
        ->name('campaigns.places.create');
    Route::post('/campaigns/{campaignSlug}/places', [NodeController::class, 'placesStore'])
        ->name('campaigns.places.store');
    Route::get('/campaigns/{campaignSlug}/places/{nodeSlug}', [NodeController::class, 'placesShow'])
        ->name('campaigns.places.show');
    Route::get('/campaigns/{campaignSlug}/places/{nodeSlug}/edit', [NodeController::class, 'placesEdit'])
        ->name('campaigns.places.edit');
    Route::put('/campaigns/{campaignSlug}/places/{nodeSlug}', [NodeController::class, 'placesUpdate'])
        ->name('campaigns.places.update');
    Route::delete('/campaigns/{campaignSlug}/places/{nodeSlug}', [NodeController::class, 'placesDestroy'])
        ->name('campaigns.places.destroy');
});

require __DIR__.'/auth.php';
