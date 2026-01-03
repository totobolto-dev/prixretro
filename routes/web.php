<?php

use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsoleController::class, 'index'])->name('home');

// Content pages (must be before console routes to avoid conflicts)
Route::get('/{console:slug}/classement', [ContentController::class, 'showRanking'])->name('content.ranking');

// Console and variant routes
Route::get('/{console:slug}', [ConsoleController::class, 'show'])->name('console.show');
Route::get('/{console:slug}/{variant:slug}', [VariantController::class, 'show'])->name('variant.show');
