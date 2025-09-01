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
            'Analisis Ambiental',
            'Analisis Instrumental',
            'Biología y Biotecnología',
            'Calidad de aire',
            'Electronica',
            'Fisica',
            'Geotecnia',
            'Ingenieria Hidraulica',
            'Materiales',
            'Maquinas y Herramientas',
            'Mecanica de Fluidos',
            'Procesos fisicos Quimicos y Biologicos',
            'Procesos y Planta piloto',
            'Quimica',
            'Ciencia,Tecnologia,Ingenieria,Artes y Matematicas -STEAM',
            'Topografia y Cartografia',
            'Vias y Pavimentos',
            'Automatizacion'
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
