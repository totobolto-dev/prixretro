<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\MarketTrendsController;
use App\Http\Controllers\QuickClassifyController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsoleController::class, 'index'])->name('home');

// Quick Classify Tool (admin only)
Route::get('/admin/quick-classify', [QuickClassifyController::class, 'index'])->name('admin.quick-classify');
Route::get('/admin/quick-classify/next', [QuickClassifyController::class, 'getNextListing'])->name('admin.quick-classify.next');
Route::post('/admin/quick-classify/{listing}', [QuickClassifyController::class, 'classify'])->name('admin.quick-classify.save');

// User Collection (DISABLED - no auth system implemented)
// Route::middleware('auth')->group(function () {
//     Route::get('/ma-collection', [CollectionController::class, 'index'])->name('collection.index');
//     Route::post('/collection/add/{variant}', [CollectionController::class, 'add'])->name('collection.add');
//     Route::delete('/collection/{collection}', [CollectionController::class, 'remove'])->name('collection.remove');
//     Route::patch('/collection/{collection}', [CollectionController::class, 'update'])->name('collection.update');
// });

// Market Trends Dashboard
Route::get('/tendances', [MarketTrendsController::class, 'index'])->name('trends.index');

// Guide pages (must be before console routes to avoid conflicts)
Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/guide-achat-game-boy-color', [GuideController::class, 'showGameBoyColorGuide'])->name('guides.game-boy-color');
Route::get('/guides/ps-vita-occasion-guide', [GuideController::class, 'showPSVitaGuide'])->name('guides.ps-vita');
Route::get('/guides/guide-game-boy-advance', [GuideController::class, 'showGameBoyAdvanceGuide'])->name('guides.game-boy-advance');
Route::get('/guides/reperer-console-retrogaming-contrefaite', [GuideController::class, 'showFakeDetectionGuide'])->name('guides.fake-detection');
Route::get('/guides/meilleures-consoles-retro-2026', [GuideController::class, 'showBestConsoles2026'])->name('guides.best-consoles-2026');
Route::get('/guides/authentifier-console-retrogaming', [GuideController::class, 'showAuthenticationGuide'])->name('guides.authentication');
Route::get('/guides/nettoyer-console-retro-jaunie', [GuideController::class, 'showCleaningGuide'])->name('guides.cleaning');
Route::get('/guides/pourquoi-prix-gba-ont-explose', [GuideController::class, 'showGBAPriceAnalysis'])->name('guides.gba-analysis');
Route::get('/guides/investir-consoles-retrogaming', [GuideController::class, 'showInvestmentGuide'])->name('guides.investment');
Route::get('/guides/guide-achat-nintendo-ds', [GuideController::class, 'showNintendoDSGuide'])->name('guides.nintendo-ds');
Route::get('/guides/psp-ou-ps-vita-quelle-console-acheter', [GuideController::class, 'showPSPVSVitaGuide'])->name('guides.psp-vs-vita');
Route::get('/guides/tester-console-occasion-avant-achat', [GuideController::class, 'showTestingGuide'])->name('guides.testing');
Route::get('/guides/estimer-valeur-collection-retrogaming', [GuideController::class, 'showValuationGuide'])->name('guides.valuation');
Route::get('/guides/guide-achat-playstation-1', [GuideController::class, 'showPlayStation1Guide'])->name('guides.playstation-1');
Route::get('/guides/guide-achat-playstation-2', [GuideController::class, 'showPlayStation2Guide'])->name('guides.playstation-2');
Route::get('/guides/guide-achat-nintendo-64', [GuideController::class, 'showNintendo64Guide'])->name('guides.nintendo-64');
Route::get('/guides/guide-achat-gamecube', [GuideController::class, 'showGameCubeGuide'])->name('guides.gamecube');
Route::get('/guides/guide-achat-super-nintendo', [GuideController::class, 'showSuperNintendoGuide'])->name('guides.super-nintendo');
Route::get('/guides/guide-achat-mega-drive', [GuideController::class, 'showMegaDriveGuide'])->name('guides.mega-drive');
Route::get('/guides/guide-achat-nes', [GuideController::class, 'showNESGuide'])->name('guides.nes');
Route::get('/guides/guide-achat-nintendo-3ds', [GuideController::class, 'showNintendo3DSGuide'])->name('guides.nintendo-3ds');

// Content pages (must be before console routes to avoid conflicts)
Route::get('/{console:slug}/classement', [ContentController::class, 'showRanking'])->name('content.ranking');

// Console and variant routes
Route::get('/{console:slug}', [ConsoleController::class, 'show'])->name('console.show');
Route::get('/{console:slug}/{variant:slug}', [VariantController::class, 'show'])->name('variant.show');
