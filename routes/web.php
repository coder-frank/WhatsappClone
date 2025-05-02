<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


Route::post('/send-login-code', [AuthController::class, 'sendCode']);
Route::post('/verify-login-code', [AuthController::class, 'verifyCode']);


Route::middleware('auth')->group(function () {


    Route::post('/start-chat', [ChatController::class, 'startChat'])->middleware('auth');
    Route::get('/messages/{friendId}', [MessageController::class, 'getMessage']);
    Route::post('/send-message', [MessageController::class, 'sendMessage']);
    Route::post('/send-media', [MessageController::class, 'sendMedia']);


    Route::get('/dashboard', [ChatController::class, 'loadDashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/test-broadcast', function () {
    $msg = \App\Models\Message::latest()->first();
    event(new \App\Events\MessageSent($msg));
    return 'Broadcasted!';
});
