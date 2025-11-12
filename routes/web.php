<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Customer\TukangMapController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TukangController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Customer\CustomerDashboardController;
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

    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/tukangs/{id}', [TukangMapController::class, 'showProfile'])->name('tukangs.show');
    Route::get('/find-tukang', [TukangMapController::class, 'index'])->name('find-tukang');
    Route::get('/api/tukangs', [TukangMapController::class, 'getTukangs'])->name('api.tukangs');
    Route::get('/api/tukangs/{tukang}', [TukangMapController::class, 'show'])->name('api.tukangs.show');

    Route::get('/chat/{receiverType}/{receiverId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('chat.messages');

    Route::post('/order/{order}/accept', [ChatController::class, 'acceptOrder'])->name('order.accept');
    Route::post('/order/{order}/reject', [ChatController::class, 'rejectOrder'])->name('order.reject');

    Route::post('/payments/process', [PaymentController::class, 'processPayment'])->name('payments.process');
});

// Tukang Dashboard
Route::middleware(['auth:tukang', 'verified'])->name('tukang.')->group(function () {
    /*Route::get('/dashboard/tukang', function () {
        return view('tukang.dashboard');
    })->name('dashboard');*/

    Route::get('/dashboard/tukang', [TukangController::class, 'dashboard'])->name('dashboard');
    Route::get('/tukang/profile', [TukangController::class, 'profile'])->name('profile');
    Route::put('/tukang/profile', [TukangController::class, 'updateProfile'])->name('profile.update');
    Route::post('/tukang/toggle-availability', [TukangController::class, 'toggleAvailability'])->name('toggle.availability');

    Route::get('/tukang/chat/{receiverType}/{receiverId}', [ChatController::class, 'showForTukang'])->name('chat.show');
    Route::post('/tukang/chat/send', [ChatController::class, 'sendMessageFromTukang'])->name('chat.send');
    Route::get('/tukang/messages/recent', [ChatController::class, 'getRecentMessagesForTukang'])->name('messages.recent');

    Route::post('/order/send', [ChatController::class, 'sendOrderProposal'])->name('order.send');
    Route::get('/services', [ChatController::class, 'getTukangServices'])->name('services');
});

// Public service routes
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/tukangs/{id}', [TukangMapController::class, 'showProfile'])->name('tukangs.show');

require __DIR__.'/auth.php';
