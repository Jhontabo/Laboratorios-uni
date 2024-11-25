<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaboratorioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('laboratorio')->insert([
            [
                'nombre' => 'Laboratorio A',
                'ubicacion' => 'Edificio 1, Piso 2',
                'capacidad' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Laboratorio B',
                'ubicacion' => 'Edificio 1, Piso 3',
                'capacidad' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
