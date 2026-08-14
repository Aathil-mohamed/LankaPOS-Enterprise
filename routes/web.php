<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:view dashboard')
        ->name('dashboard');

    // Products Management Routes
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])
        ->middleware('can:manage products')
        ->name('products.index');
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])
        ->middleware('can:manage products')
        ->name('products.store');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])
        ->middleware('can:manage products')
        ->name('products.destroy');
    Route::post('/products/{product}/stock', [\App\Http\Controllers\ProductController::class, 'updateStock'])
        ->middleware('can:manage products')
        ->name('products.update-stock');

    // Multi-Store / Branch Routes
    Route::get('/stores', [\App\Http\Controllers\StoreController::class, 'index'])
        ->middleware('can:manage settings')
        ->name('stores.index');
    Route::post('/stores', [\App\Http\Controllers\StoreController::class, 'store'])
        ->middleware('can:manage settings')
        ->name('stores.store');

    // POS Routes
    Route::get('/pos', [PosController::class, 'index'])
        ->middleware('can:access pos')
        ->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])
        ->middleware('can:access pos')
        ->name('pos.store');
    Route::get('/pos/receipt/{id}', [PosController::class, 'printReceipt'])
        ->middleware('can:access pos')
        ->name('pos.receipt');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
