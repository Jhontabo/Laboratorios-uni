<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Usa el modelo User
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o verificar que existan los roles
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $docenteRole = Role::firstOrCreate(['name' => 'DOCENTE']);

        // Crear el usuario Admin si no existe
        $admin = User::firstOrCreate(
            ['correo_electronico' => 'jhonse.tajumbina@umariana.edu.co'],
            [
                'nombre' => 'Jhon',
                'apellido' => 'Tajumbina',
                'telefono' => '123456789',
                'Direccion' => 'Calle Falsa 123',
            ]
        );

        // Asignar el rol de Admin al usuario Admin
        if (!$admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        // Crear el usuario Docente si no existe
        $docente = User::firstOrCreate(
            ['correo_electronico' => 'jonathanc.burbano221@umariana.edu.co'],
            [
                'nombre' => 'Jonathan',
                'apellido' => 'Burbano',
                'telefono' => '987654321',
                'Direccion' => 'Calle Real 456',
            ]
        );

        // Asignar el rol de Docente al usuario Docente
        if (!$docente->hasRole($docenteRole)) {
            $docente->assignRole($docenteRole);
        }

        $this->command->info('Usuarios y roles asignados correctamente.');
    }
}
