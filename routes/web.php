<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::prefix('api')->group(function () {
    Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::post('/register', [RegisterUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user/me', [AuthenticatedSessionController::class, 'me'])->name('user.me');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/urls', [UrlController::class, 'index'])->name('urls.index');
        Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');
        Route::delete('/urls/{url}', [UrlController::class, 'destroy'])->name('urls.destroy');
    });
});
