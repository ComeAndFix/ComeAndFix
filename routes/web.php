<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\HandymanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\TukangAuthController;

// Customer Authentication Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// Tukang Authentication Routes
Route::prefix('tukang')->name('tukang.')->group(function () {
    Route::get('/login', [TukangAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [TukangAuthController::class, 'login']);
    Route::get('/register', [TukangAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [TukangAuthController::class, 'register']);
    Route::post('/logout', [TukangAuthController::class, 'logout'])->name('logout');
});

use Illuminate\Support\Facades\Auth;

// Customer Dashboard
Route::middleware(['auth:customer', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

    // Handyman routes
    Route::get('/handymen/{id}', [HandymanController::class, 'show'])->name('handymen.show');
});

// Tukang Dashboard
Route::middleware(['auth:tukang', 'verified'])->prefix('tukang')->name('tukang.')->group(function () {
    Route::get('/dashboard', function () {
        return view('tukang.dashboard');
    })->name('dashboard');
});

// Public service routes (for non-authenticated users or general access)
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/handymen/{id}', [HandymanController::class, 'show'])->name('handymen.show');

require __DIR__.'/auth.php';
