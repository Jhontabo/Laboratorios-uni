<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    // Dominios de correo permitidos
    protected $allowedDomains = ['umariana.edu.co'];

    // Roles permitidos en el sistema
    protected $allowedRoles = [
        'ADMIN',
        'LABORATORISTA',
        'DOCENTE',
        'ESTUDIANTE'
    ];

    // Redirigir a Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['hd' => 'umariana.edu.co'])
            ->redirect();
    }

    // Manejar el callback de Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = $this->getGoogleUser();
            $this->logAuthenticationAttempt($googleUser);

            if (!$this->isValidDomain($googleUser->getEmail())) {
                return $this->redirectWithError('Dominio de correo no permitido');
            }

            $user = $this->findOrCreateUser($googleUser);

            if (!$this->userHasValidRole($user)) {
                return $this->redirectWithError('No tienes permisos para acceder');
            }

            Auth::login($user, $remember = true);

            return $this->redirectToDashboard($user);
        } catch (\Exception $e) {
            Log::error('Error en autenticación Google', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->redirectWithError(
                'Error en autenticación: ' . $e->getMessage()
            );
        }
    }


    /**
     * Obtiene los datos del usuario desde Google
     * 
     * @return \Laravel\Socialite\Contracts\User
     */
    protected function getGoogleUser()
    {
        return Socialite::driver('google')->user();
    }

    /**
     * Registra el intento de autenticación
     * 
     * @param \Laravel\Socialite\Contracts\User $googleUser
     */
    protected function logAuthenticationAttempt($googleUser)
    {
        Log::info('Intento de autenticación Google', [
            'email' => $googleUser->getEmail(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Valida el dominio del correo electrónico
     * 
     * @param string $email
     * @return bool
     */
    protected function isValidDomain(string $email): bool
    {
        $domain = Str::afterLast($email, '@');
        return in_array($domain, $this->allowedDomains);
    }

    /**
     * Busca o crea un usuario basado en los datos de Google
     * 
     * @param \Laravel\Socialite\Contracts\User $googleUser
     * @return \App\Models\User
     */
    protected function findOrCreateUser($googleUser): User
    {
        return User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
        );
    }

    /**
     * Verifica si el usuario tiene un rol válido
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    protected function userHasValidRole(User $user): bool
    {
        return $user->roles()->whereIn('name', $this->allowedRoles)->exists();
    }

    /**
     * Redirige al dashboard según el rol del usuario
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */


    protected function redirectToDashboard(User $user)
    {
        $route = match (true) {
            $user->hasRole('ADMIN') => 'admin.dashboard',
            $user->hasRole('LABORATORISTA') => 'laboratorista.dashboard',
            $user->hasRole('DOCENTE') => 'docente.dashboard',
            $user->hasRole('ESTUDIANTE') => 'estudiante.dashboard',
            default => 'home'
        };

        return redirect()->route($route);
    }



    protected function redirectWithError(string $message)
    {
        return redirect('/')
            ->with('error', $message);
    }
}
