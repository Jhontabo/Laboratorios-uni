<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('usuarios')->insert([
            'nombre' => 'Jhon',
            'apellido' => 'Tajumbina',
            'correo_electronico' => 'jhonse.tajumbina@umariana.edu.co',
            'telefono' => '123456789',
            'Direccion' => 'Calle Falsa 123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('usuarios')->insert([
            'nombre' => 'Jonathan',
            'apellido' => 'Burbano',
            'correo_electronico' => 'jonathanc.burbano221@umariana.edu.co',
            'telefono' => '987654321',
            'Direccion' => 'Avenida Siempreviva 456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
