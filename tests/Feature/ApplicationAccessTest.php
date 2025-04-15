<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApplicationAccessTest extends TestCase
{
    use DatabaseTransactions;


    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function test_home_page_loads_fails(): void
    {
        // Usuario no autenticado - debe recibir 404
        $response = $this->get('/dashboard');
        $response->assertStatus(404);
    }
}
