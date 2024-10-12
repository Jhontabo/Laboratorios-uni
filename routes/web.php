<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Ruta para la página de inicio (redirige al login directamente)
Route::get('/', function () {
    return view('auth.login');  // Carga la vista de login que creaste previamente
});

// Ruta para mostrar la página de login (esta puede ser la misma que en el inicio)
Route::get('login', function () {
    return view('auth.login');  // Vista de login
})->name('login');

// Ruta para la autenticación con Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// Ruta de callback para Google
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Rutas protegidas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        // Redirigir al panel de Filament
        return redirect()->route('filament.admin.pages.dashboard');
    })->name('dashboard');
});

// Ruta protegida para verificar el usuario autenticado
Route::middleware(['auth'])->get('/test-auth', function () {
    // Mostrar información del usuario autenticado
    return response()->json([
        'user' => auth()->user(),  // Devuelve el usuario autenticado
        'name' => auth()->user()->getUserName(),  // Devuelve el nombre del usuario usando getUserName()
    ]);
});