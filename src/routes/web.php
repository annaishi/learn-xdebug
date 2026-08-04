<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\PlaygroundController;
use Illuminate\Support\Facades\Route;

// トップページはログイン画面へ
Route::get('/', fn () => redirect()->route('login'));

// Xdebug 操作の練習用ページ（ログイン不要）
Route::get('/playground', [PlaygroundController::class, 'index'])->name('playground');
Route::post('/playground', [PlaygroundController::class, 'calculate'])->name('playground.calculate');

// Xdebug 演習ページ（バグ入り・ログイン不要）
Route::get('/exercise', [ExerciseController::class, 'index'])->name('exercise');
Route::post('/exercise', [ExerciseController::class, 'calculate'])->name('exercise.calculate');

// --- 未ログインのユーザー向け (guest ミドルウェア) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// --- ログイン済みのユーザー向け (auth ミドルウェア) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
