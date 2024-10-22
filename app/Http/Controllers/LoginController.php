<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;  // Asegúrate de que estás importando tu modelo Usuario
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        // Este es el punto donde parece estar el problema. El método `stateless` debería funcionar aquí.
        $googleUser = Socialite::driver('google')->user();

        // Loguear los datos del usuario de Google para debug
        Log::info('Usuario autenticado con Google:', [
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'avatar' => $googleUser->getAvatar(),
        ]);

        // Intentar encontrar al usuario en la base de datos
        $user = Usuario::where('correo_electronico', $googleUser->getEmail())->first();

        if ($user) {
            Auth::login($user);
            return redirect('/dashboard');
        } else {
            Notification::make()
                ->title('Acceso denegado')
                ->danger()
                ->body('El usuario no está autorizado para acceder.')
                ->send();

            return redirect('/');
        }
    }
}
