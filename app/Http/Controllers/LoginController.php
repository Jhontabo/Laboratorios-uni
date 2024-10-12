<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class LoginController extends Controller
{
    // Redirigir a Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Manejar el callback de Google
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Buscar si ya existe el usuario
        $user = Usuario::where('correo_electronico', $googleUser->getEmail())->first();

        if ($user) {
            // Si el usuario existe, lo autenticamos
            Auth::login($user);
            return redirect('/dashboard');  // Redirige al dashboard
        } else {
            // Si no existe, mostramos una notificación de acceso denegado
            Notification::make()
                ->title('Acceso denegado')
                ->danger()
                ->body('El usuario no está autorizado para acceder.')
                ->send();
            
            return redirect()->route('login');  // Redirige de vuelta al login
        }
    }
}
