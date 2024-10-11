<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Ruta para la página de inicio (puedes cambiar esta a tu vista de login directamente si quieres)
Route::get('/', function () {
    return view('auth.login');  // Redirige a la página de login que creaste
});

// Ruta para mostrar la página de login
Route::get('login', function () {
    return view('auth.login');  // Ruta para la vista de login
})->name('login');

// Ruta para la autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// Ruta de callback para Google
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Puedes agregar aquí rutas para cuando el usuario esté autenticado
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Ruta de ejemplo para un dashboard o vista protegida
    })->name('dashboard');
});
