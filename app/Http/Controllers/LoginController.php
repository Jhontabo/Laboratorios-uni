<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
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
        try {
            // Obtener datos del usuario desde Google
            $googleUser = Socialite::driver('google')->user();

            // Loguear los datos del usuario de Google para depuración
            Log::info('Usuario autenticado con Google:', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            // Intentar encontrar al usuario en la base de datos
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Iniciar sesión al usuario autorizado
                Auth::login($user);

                // Redirigir al área protegida
                return redirect('/admin');
            } else {
                // Validar el dominio del correo electrónico
                $emailDomain = substr(strrchr($googleUser->getEmail(), "@"), 1);
                if ($emailDomain !== 'umariana.edu.co') {
                    // Notificar acceso denegado si el correo no pertenece al dominio autorizado
                    Notification::make()
                        ->title('Acceso denegado')
                        ->danger()
                        ->body('El correo electrónico no pertenece a la Universidad Mariana.')
                        ->send();

                    return redirect('/');
                }

                // Si llega aquí, el usuario no está registrado en el sistema
                Notification::make()
                    ->title('Acceso denegado')
                    ->danger()
                    ->body('El usuario no está autorizado para acceder.')
                    ->send();

                return redirect('/');
            }
        } catch (\Exception $e) {
            // Manejo de errores
            Log::error('Error en el inicio de sesión con Google:', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Error de autenticación')
                ->danger()
                ->body('Hubo un problema al autenticar con Google.')
                ->send();

            return redirect('/');
        }
    }
}
