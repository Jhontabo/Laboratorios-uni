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
            'COORDINADOR'
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
                'document_number' => '123456789',

            ],
            [
                'email' => 'jonathanc.burbano221@umariana.edu.co',
                'name' => 'Jonathan',
                'last_name' => 'Burbano',
                'phone' => '987654321',
                'address' => 'Calle Real 456',
                'role' => 'ESTUDIANTE',
                'document_number' => '123456789',


            ],
            [
                'email' => 'laboratorista@ejemplo.com',
                'name' => 'Estefany',
                'last_name' => 'Lopez',
                'phone' => '321654987',
                'address' => 'Calle Laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'laboratorista1@ejemplo.com',
                'name' => 'Hugo',
                'last_name' => 'Espinoza',
                'phone' => '321654987',
                'address' => 'Calle Laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'laboratorista2@ejemplo.com',
                'name' => 'Edith',
                'last_name' => 'Santacruz',
                'phone' => '321654987',
                'address' => 'Calle Laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'laboratorista3@ejemplo.com',
                'name' => 'harol',
                'last_name' => 'santacruz',
                'phone' => '321654987',
                'address' => 'calle laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'laboratorista4@ejemplo.com',
                'name' => 'Martin',
                'last_name' => 'Moncayo',
                'phone' => '321654987',
                'address' => 'calle laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],


            [
                'email' => 'laboratorista5@ejemplo.com',
                'name' => 'Jhon fredy',
                'last_name' => 'Moncayo',
                'phone' => '321654987',
                'address' => 'calle laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'laboratorista6@ejemplo.com',
                'name' => 'Julian',
                'last_name' => 'Reina',
                'phone' => '321654987',
                'address' => 'calle laboratorio 789',
                'role' => 'LABORATORISTA',
                'document_number' => '123456789',


            ],

            [
                'email' => 'estudiante@ejemplo.com',
                'name' => 'Carlos',
                'last_name' => 'López',
                'phone' => '654987321',
                'address' => 'Avenida Universidad 456',
                'role' => 'ESTUDIANTE',
                'document_number' => '123456789',


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
                    'document_number' => '110005671'
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
