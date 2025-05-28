<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratory;
use App\Models\User;

class LaboratoriesSeeder extends Seeder
{
    public function run()
    {
        $laboratoristas = User::role('LABORATORISTA')->get();

        if ($laboratoristas->isEmpty()) {
            $this->command->info('No hay usuarios con el rol "LABORATORISTA".');
            return;
        }

        $labNames = [
            'Química',
            'Física',
            'Biología y Biotecnología',
            'Análisis Instrumental',
            'Operaciones Unitarias',
            'Máquinas y Herramientas',
            'Electrónica',
            'Automatización',
            'Fluidos',
            'Procesos y Plantas Piloto',
            'Materiales',
            'Geotecnia',
            'Vías y Pavimentos',
            'Robótica y Control',
            'Análisis Ambiental',
        ];

        foreach ($labNames as $name) {
            Laboratory::create([
                'name'     => $name,
                'location' => 'Edificio principal, Piso 1',
                'capacity' => rand(10, 50),
                'user_id'  => $laboratoristas->random()->id,
            ]);
        }

        $this->command->info(count($labNames) . ' laboratorios creados correctamente.');
    }
}
