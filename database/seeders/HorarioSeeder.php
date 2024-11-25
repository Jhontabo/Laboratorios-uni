<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horario')->insert([
            [
                'id_laboratorista' => 1,
                'id_laboratorio' => 1,
                'dia_semana' => 'Lunes',
                'hora_inicio' => '08:00',
                'hora_fin' => '10:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_laboratorista' => 2,
                'id_laboratorio' => 2,
                'dia_semana' => 'Martes',
                'hora_inicio' => '14:00',
                'hora_fin' => '16:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
