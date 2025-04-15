<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

// Ruta principal - login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
})->name('login'); // Añade el nombre 'login'

// Autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Ruta dashboard - comportamiento modificado
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        abort(404); // Devuelve 404 para usuarios no autenticados
    }

    $user = Auth::user();

    if ($user->hasRole('ADMIN')) return redirect('/admin');
    if ($user->hasRole('LABORATORISTA')) return redirect('/laboratorista');
    if ($user->hasRole('DOCENTE')) return redirect('/docente');
    if ($user->hasRole('ESTUDIANTE')) return redirect('/estudiante');

    return redirect('/')->with('error', 'No tienes un rol asignado.');
})->name('dashboard');
