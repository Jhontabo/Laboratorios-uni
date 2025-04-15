<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;


class AdminAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_access_admin(): void
    {

        // 1. Usuario no autenticado accede a la ruta raíz
        $response = $this->get('/');

        // 2. Verificar que muestra la vista de login
        $response->assertSuccessful()
            ->assertViewIs('auth.login');
    }

    public function test_redirects_to_google_auth_page()
    {
        // 1. Hacer la petición a la ruta de autenticación
        $response = $this->get('/auth/google');

        // 2. Verificar que redirige a Google
        $response->assertRedirect();

        // 3. Verificar que la URL contiene el dominio de autenticación de Google
        $this->assertStringContainsString(
            'https://accounts.google.com/o/oauth2/auth',
            $response->headers->get('Location')
        );

        // 4. Verificar parámetros importantes en la URL
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('response_type=code', $redirectUrl);
        $this->assertStringContainsString('scope=openid', $redirectUrl);
        $this->assertStringContainsString('hd=umariana.edu.co', $redirectUrl);
    }


    public function test_google_auth_url_contains_correct_client_id()
    {
        // Configurar el client_id esperado
        $clientId = config('services.google.client_id');

        $response = $this->get('/auth/google');
        $redirectUrl = $response->headers->get('Location');

        // Verificar que la URL contiene el client_id correcto
        $this->assertStringContainsString("client_id={$clientId}", $redirectUrl);
    }
}
