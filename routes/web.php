<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

// Ruta principal - login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
})->name('login');

// Autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Ruta dashboard - TODOS van a /admin si están autenticados
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        abort(404); // Devuelve 404 para usuarios no autenticados
    }

    // Todos los usuarios van al dashboard admin
    return redirect('/admin');
})->name('dashboard');



Route::middleware(['web', 'auth'])->get('/calendar-only', fn() => view('filament.pages.booking-calendar'));
