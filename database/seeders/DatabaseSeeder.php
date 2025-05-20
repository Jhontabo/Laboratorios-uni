<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            LaboratoriesSeeder::class,
            PermissionsSeeder::class,
            ProductsSeeder::class,
            AcademicProgramSeeder::class,

        ]);
    }
}
