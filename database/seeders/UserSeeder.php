<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $docenteRole = Role::firstOrCreate(['name' => 'DOCENTE']);
        $laboratoristaRole = Role::firstOrCreate(['name' => 'LABORATORISTA']);
        $estudianteRole = Role::firstOrCreate(['name' => 'ESTUDIANTE']);

        // Crear usuarios
        $usuarios = [
            [
                'correo_electronico' => 'jhonse.tajumbina@umariana.edu.co',
                'nombre' => 'Jhon',
                'apellido' => 'Tajumbina',
                'telefono' => '123456789',
                'direccion' => 'Calle Falsa 123',
                'rol' => $adminRole,
            ],
            [
                'correo_electronico' => 'jonathanc.burbano221@umariana.edu.co',
                'nombre' => 'Jonathan',
                'apellido' => 'Burbano',
                'telefono' => '987654321',
                'direccion' => 'Calle Real 456',
                'rol' => $docenteRole,
            ],
            [
                'correo_electronico' => 'laboratorista@ejemplo.com',
                'nombre' => 'Laura',
                'apellido' => 'García',
                'telefono' => '321654987',
                'direccion' => 'Calle Laboratorio 789',
                'rol' => $laboratoristaRole,
            ],
            [
                'correo_electronico' => 'estudiante@ejemplo.com',
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'telefono' => '654987321',
                'direccion' => 'Avenida Universidad 456',
                'rol' => $estudianteRole,
            ],
        ];

        foreach ($usuarios as $data) {
            $user = User::firstOrCreate(
                ['correo_electronico' => $data['correo_electronico']],
                [
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'telefono' => $data['telefono'],
                    'direccion' => $data['direccion'],
                ]
            );

            if (!$user->hasRole($data['rol'])) {
                $user->assignRole($data['rol']);
            }
        }

        $this->command->info('Usuarios y roles asignados correctamente.');
    }
}
