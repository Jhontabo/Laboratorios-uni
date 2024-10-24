<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Database\Seeders\str_random;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('users')->insert([
            'nombre' => 'Jhon',
            'apellido' => 'Tajumbina',
            'correo_electronico' => 'jhonse.tajumbina@umariana.edu.co',
            'telefono' => '123456789',
            'Direccion' => 'Calle Falsa 123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
