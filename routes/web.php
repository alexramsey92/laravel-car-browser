<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\AdminController;

Route::get('/', [CarController::class, 'index'])->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/cars', [AdminController::class, 'cars'])->name('cars');
    Route::get('/scrape', [AdminController::class, 'scrape'])->name('scrape');
    Route::post('/scrape', [AdminController::class, 'runScraper'])->name('scrape.run');
});
