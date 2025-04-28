<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $roles = [
            'ADMIN',
            'DOCENTE',
            'LABORATORISTA',
            'ESTUDIANTE',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear usuarios con sus roles
        $users = [
            [
                'email' => 'jhonse.tajumbina@umariana.edu.co',
                'name' => 'Jhon',
                'last_name' => 'Tajumbina',
                'phone' => '123456789',
                'address' => 'Calle Falsa 123',
                'role' => 'ADMIN',
            ],
            [
                'email' => 'jonathanc.burbano221@umariana.edu.co',
                'name' => 'Jonathan',
                'last_name' => 'Burbano',
                'phone' => '987654321',
                'address' => 'Calle Real 456',
                'role' => 'ADMIN',
            ],
            [
                'email' => 'laboratorista@ejemplo.com',
                'name' => 'Laura',
                'last_name' => 'García',
                'phone' => '321654987',
                'address' => 'Calle Laboratorio 789',
                'role' => 'LABORATORISTA',
            ],
            [
                'email' => 'estudiante@ejemplo.com',
                'name' => 'Carlos',
                'last_name' => 'López',
                'phone' => '654987321',
                'address' => 'Avenida Universidad 456',
                'role' => 'ESTUDIANTE',
            ],
            [
                'email' => 'danielf.zapata221@umariana.edu.co',
                'name' => 'Daniel',
                'last_name' => 'Zapata',
                'phone' => '741258963',
                'address' => 'Avenida Docente 123',
                'role' => 'DOCENTE',
            ],
            [
                'email' => 'ivanda.martinez@umariana.edu.co',
                'name' => 'Iván Darío',
                'last_name' => 'Martínez',
                'phone' => '852963741',
                'address' => 'Calle Campus 789',
                'role' => 'ESTUDIANTE',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'status' => 'active',
                ]
            );

            $role = Role::where('name', $data['role'])->first();

            if ($role && !$user->hasRole($role->name)) {
                $user->assignRole($role->name);
            }
        }

        $this->command->info('Users and roles assigned successfully.');
    }
}
