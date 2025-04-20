<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\OnlyAdmin;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Illuminate\Support\Facades\File;

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

        Route::middleware(OnlyAdmin::class)->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/urls/{url}', [UrlController::class, 'show'])->name('urls.show');
            Route::get('/urls/{url}/visitors', [UrlController::class, 'visitors'])->name('urls.visitors');
            Route::patch('/urls/{url}', [UrlController::class, 'update'])->name('urls.update');

            Route::get('/users', [UserController::class, 'index'])->name('users.index');
        });
    });
});

Route::get('/{any}', function () {
    return File::get(public_path('../client/.output/index.mjs'));
})->where('any', '.*');
