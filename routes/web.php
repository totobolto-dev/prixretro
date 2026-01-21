<?php

use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsoleController::class, 'index'])->name('home');

// Guide pages (must be before console routes to avoid conflicts)
Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/game-boy-color', [GuideController::class, 'showGameBoyColorGuide'])->name('guides.game-boy-color');
Route::get('/guides/ps-vita', [GuideController::class, 'showPSVitaGuide'])->name('guides.ps-vita');
Route::get('/guides/game-boy-advance', [GuideController::class, 'showGameBoyAdvanceGuide'])->name('guides.game-boy-advance');
Route::get('/guides/fake-detection', [GuideController::class, 'showFakeDetectionGuide'])->name('guides.fake-detection');
Route::get('/guides/best-consoles-2026', [GuideController::class, 'showBestConsoles2026'])->name('guides.best-consoles-2026');

// Content pages (must be before console routes to avoid conflicts)
Route::get('/{console:slug}/classement', [ContentController::class, 'showRanking'])->name('content.ranking');

// Console and variant routes
Route::get('/{console:slug}', [ConsoleController::class, 'show'])->name('console.show');
Route::get('/{console:slug}/{variant:slug}', [VariantController::class, 'show'])->name('variant.show');
