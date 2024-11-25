<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('productos')->insert([
            [
                'nombre' => 'Reactivo Químico',
                'descripcion' => 'Reactivo para experimentos de laboratorio.',
                'cantidad_disponible' => 100,
                'id_laboratorio' => 1,
                'id_categorias' => 1,
                'numero_serie' => 'RQ-2024-001',
                'ubicacion' => 'Almacén 1',
                'estado' => 'nuevo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Microscopio',
                'descripcion' => 'Microscopio de alta precisión.',
                'cantidad_disponible' => 10,
                'id_laboratorio' => 2,
                'id_categorias' => 2,
                'numero_serie' => 'MS-2024-002',
                'ubicacion' => 'Laboratorio de Biología',
                'estado' => 'usado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
