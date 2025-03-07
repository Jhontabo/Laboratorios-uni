<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

            // Validar el dominio del correo electrónico
            $emailDomain = substr(strrchr($googleUser->getEmail(), "@"), 1);
            if ($emailDomain !== 'umariana.edu.co') {
                // Redirigir con un mensaje de sesión si el dominio no es permitido
                return redirect('/')
                    ->with('error', 'El correo electrónico no pertenece a la Universidad Mariana.');
            }

            // Intentar encontrar al usuario en la base de datos
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Iniciar sesión al usuario autorizado
                Auth::login($user);

                // Verificar permisos del usuario
                if ($this->hasAccess($user)) {
                    // Redirigir según el rol del usuario
                    return $this->redirectUserBasedOnRole($user);
                } else {
                    return redirect('/')
                        ->with('error', 'No tienes permisos para acceder al sistema.');
                }
            } else {
                // Si el usuario no está registrado, mostrar un mensaje
                return redirect('/')
                    ->with('error', 'No estás autorizado para acceder al sistema.');
            }
        } catch (\Exception $e) {
            // Manejo de errores
            Log::error('Error en el inicio de sesión con Google:', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/')
                ->with('error', 'Hubo un problema al autenticar con Google.');
        }
    }

    // Verificar si el usuario tiene acceso
    private function hasAccess(User $user)
    {
        // Verifica si el usuario tiene uno de los roles permitidos
        return $user->hasRole('ADMIN') || $user->hasRole('LABORATORISTA') || $user->hasRole('DOCENTE') || $user->hasRole('ESTUDIANTE');
    }

    private function redirectUserBasedOnRole(User $user)
    {
        if ($user->hasRole('ADMIN')) {
            return redirect('/admin');
        } elseif ($user->hasRole('LABORATORISTA')) {
            return redirect('/laboratorista');
        } elseif ($user->hasRole('DOCENTE')) {
            return redirect('/docente');
        } elseif ($user->hasRole('ESTUDIANTE')) {
            return redirect('/estudiante');
        } else {
            return redirect('/')
                ->with('error', 'No tienes un rol asignado.');
        }
    }
}