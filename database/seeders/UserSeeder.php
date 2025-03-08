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
        $roles = [
            'ADMIN',
            'DOCENTE',
            'LABORATORISTA',
            'ESTUDIANTE'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear usuarios con sus roles
        $usuarios = [
            [
                'email' => 'jhonse.tajumbina@umariana.edu.co',
                'name' => 'Jhon',
                'apellido' => 'Tajumbina',
                'telefono' => '123456789',
                'direccion' => 'Calle Falsa 123',

                'rol' => 'ADMIN',
            ],
            [
                'email' => 'jonathanc.burbano221@umariana.edu.co',
                'name' => 'Jonathan',
                'apellido' => 'Burbano',
                'telefono' => '987654321',
                'direccion' => 'Calle Real 456',

                'rol' => 'ADMIN',
            ],
            [
                'email' => 'laboratorista@ejemplo.com',
                'name' => 'Laura',
                'apellido' => 'García',
                'telefono' => '321654987',
                'direccion' => 'Calle Laboratorio 789',

                'rol' => 'LABORATORISTA',
            ],
            [
                'email' => 'estudiante@ejemplo.com',
                'name' => 'Carlos',
                'apellido' => 'López',
                'telefono' => '654987321',
                'direccion' => 'Avenida Universidad 456',

                'rol' => 'ESTUDIANTE',
            ],
        ];

        foreach ($usuarios as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'apellido' => $data['apellido'],
                    'telefono' => $data['telefono'],
                    'direccion' => $data['direccion'],

                ]
            );

            $role = Role::where('name', $data['rol'])->first(); // ✅ Buscar el rol como string

            if ($role && !$user->hasRole($role->name)) {
                $user->assignRole($role->name);
            }
        }

        $this->command->info('Usuarios y roles asignados correctamente.');
    }
}
