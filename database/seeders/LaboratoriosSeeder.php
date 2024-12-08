<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratorio;
use App\Models\User;

class LaboratoriosSeeder extends Seeder
{
    public function run()
    {
        // Asegúrate de que existan usuarios con el rol 'LABORATORISTA'
        $laboratoristas = User::role('LABORATORISTA')->get();

        if ($laboratoristas->isEmpty()) {
            $this->command->info('No se encontraron usuarios con el rol "LABORATORISTA".');
            return;
        }

        // Insertar 10 laboratorios
        foreach (range(1, 10) as $i) {
            Laboratorio::create([
                'nombre' => "Laboratorio $i",
                'ubicacion' => "Edificio $i, Piso $i",
                'capacidad' => rand(10, 50),
                'id_usuario' => $laboratoristas->random()->id_usuario, // Asigna un laboratorista aleatorio
            ]);
        }
    }
}
