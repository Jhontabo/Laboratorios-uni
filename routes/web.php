<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReservaController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
});


// Ruta para la autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// Ruta de callback para Google
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Rutas protegidas con el middleware 'auth'
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->hasRole('ADMIN')) {
            return redirect('/admin');
        } elseif ($user->hasRole('LABORATORISTA')) {
            return redirect('/laboratorista');
        } elseif ($user->hasRole('DOCENTE')) {
            return redirect('/docente');
        } elseif ($user->hasRole('ESTUDIANTE')) {
            return redirect('/estudiante');
        } else {
            return redirect('/')->with('error', 'No tienes un rol asignado.');
        }
    })->name('dashboard');
});
