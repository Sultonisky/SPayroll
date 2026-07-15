<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('admin/login', [AuthController::class, 'loginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('admin/login', [AuthController::class, 'authLogin'])
    ->name('admin.login')
    ->middleware(['guest', 'throttle:login']);

Route::post('admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth');

// Protected Routes (example)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('admin.dashboard');
});
