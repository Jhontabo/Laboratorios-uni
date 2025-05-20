<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // Allowed email domains
    protected $allowedDomains = ['umariana.edu.co'];

    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['hd' => 'umariana.edu.co'])
            ->redirect();
    }

    // Handle the Google callback
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
     * Get user data from Google
     *
     * @return \Laravel\Socialite\Contracts\User
     */
    protected function getGoogleUser()
    {
        return Socialite::driver('google')->user();
    }

    /**
     * Log authentication attempt
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
     * Validate email domain
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
     * Find or create a user based on Google data
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
     * Check if the user has at least one role
     *
     * @param \App\Models\User $user
     * @return bool
     */
    protected function userHasValidRole(User $user): bool
    {
        // Allow if the user has at least one role, regardless of its name
        return $user->roles()->exists();
    }

    /**
     * Redirect all users to the admin dashboard, regardless of their role
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard(User $user)
    {
        // All users go to admin.dashboard
        return redirect()->route('/admin');
    }

    protected function redirectWithError(string $message)
    {
        return redirect('/')
            ->with('error', $message);
    }
}
