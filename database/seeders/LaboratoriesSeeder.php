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
      'Electrónica',
      'Física',
      'Geotecnia',
      'Ingeniería Hidráulica',
      'Materiales',
      'Máquinas y Herramientas',
      'Fluidos',
      'Procesos físicos Químicos y Biológicos',
      'Procesos y Planta piloto',
      'Química',
      'Fisico Química',
      'Ciencia, Tecnología, Ingeniería, Artes y Matemáticas - STEAM',
      'Topografía',
      'Vías y Pavimentos',
      'Automatización',
      'Operaciones Unitarias', // 👉 agregado porque lo usas en SchedulesSeeder
    ];


    foreach ($labNames as $name) {
      Laboratory::create([
        'name'     => $name,
        'location' => 'Edificio principal, Piso 2',
        'capacity' => rand(11, 50),
        'user_id'  => $laboratoristas->random()->id,
      ]);
    }

    $this->command->info(count($labNames) . ' laboratorios creados correctamente.');
  }
}
