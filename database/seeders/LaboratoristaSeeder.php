<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaboratoristaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('laboratorista')->insert([
            [
                'id_usuario' => 1, // Asegúrate de que coincida con los IDs generados en UserSeeder
                'estado' => 'Activo',
                'fecha_ingreso' => Carbon::now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_usuario' => 2,
                'estado' => 'Activo',
                'fecha_ingreso' => Carbon::now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
