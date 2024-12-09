<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
});


// Ruta para la autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// Ruta de callback para Google
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Rutas protegidas con el middleware 'auth'
Route::middleware(['auth'])->group(function () {
    // Ruta para el dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('filament.admin.pages.dashboard');
    })->name('dashboard');

    // Ruta para otra vista protegida
    Route::get('/app', function () {
        return view('app');
    });
});
