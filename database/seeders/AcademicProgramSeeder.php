<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use Illuminate\Database\Seeder;

class AcademicProgramSeeder extends Seeder
{
    public function run()
    {
        $programs = [
            ['name' => 'Ingeniería de Sistemas', 'code' => 'ISIS'],
            ['name' => 'Ingeniería Electrónica', 'code' => 'IELEC'],
            ['name' => 'Licenciatura en Matemáticas', 'code' => 'LMAT'],
        ];

        foreach ($programs as $program) {
            AcademicProgram::create($program);
        }
    }
}
