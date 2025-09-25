<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Customer\TukangMapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customer.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\TukangAuthController;

// Tukang Authentication Routes
Route::middleware('guest:tukang')->name('tukang.')->group(function () {
    Route::get('/login/tukang', [TukangAuthController::class, 'showLogin'])->name('login');
    Route::post('/login/tukang', [TukangAuthController::class, 'login']);
    Route::get('/register/tukang', [TukangAuthController::class, 'showRegister'])->name('register');
    Route::post('/register/tukang', [TukangAuthController::class, 'register']);
});

Route::middleware('auth:tukang')->name('tukang.')->group(function () {
    Route::post('/logout/tukang', [TukangAuthController::class, 'logout'])->name('logout');
});

// Customer Dashboard
Route::middleware(['auth:customer', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/tukangs/{id}', [TukangMapController::class, 'showProfile'])->name('tukangs.show');
    Route::get('/find-tukang', [TukangMapController::class, 'index'])->name('find-tukang');
    Route::get('/api/tukangs', [TukangMapController::class, 'getTukangs'])->name('api.tukangs');
    Route::get('/api/tukangs/{tukang}', [TukangMapController::class, 'show'])->name('api.tukangs.show');
});

// Tukang Dashboard
Route::middleware(['auth:tukang', 'verified'])->name('tukang.')->group(function () {
    Route::get('/dashboard/tukang', function () {
        return view('tukang.dashboard');
    })->name('dashboard');
});

// Public service routes
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/tukangs/{id}', [TukangMapController::class, 'showProfile'])->name('tukangs.show');

require __DIR__.'/auth.php';
