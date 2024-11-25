<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categorias')->insert([
            ['nombre_categoria' => 'Consumibles', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_categoria' => 'No consumibles', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
