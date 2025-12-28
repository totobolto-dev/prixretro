<?php

use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsoleController::class, 'index'])->name('home');
Route::get('/{console:slug}', [ConsoleController::class, 'show'])->name('console.show');
Route::get('/{console:slug}/{variant:slug}', [VariantController::class, 'show'])->name('variant.show');
