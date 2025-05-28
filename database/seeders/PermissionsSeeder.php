<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear roles
        $admin                = Role::firstOrCreate(['name' => 'ADMIN',         'guard_name' => 'web']);
        $laboratoryTechnician = Role::firstOrCreate(['name' => 'LABORATORISTA', 'guard_name' => 'web']);
        $teacher              = Role::firstOrCreate(['name' => 'DOCENTE',       'guard_name' => 'web']);
        $student              = Role::firstOrCreate(['name' => 'ESTUDIANTE',    'guard_name' => 'web']);
        $coordinator          = Role::firstOrCreate(['name' => 'COORDINADOR',   'guard_name' => 'web']);


        $permissions = [
            // — Paneles —
            'ver escritorio',
            'ver mi perfil',
            'ver panel de productos',
            'ver panel de historial de reservas',
            'ver panel de horarios',
            'ver panel de laboratorios',
            'ver panel de permisos',
            'ver panel de roles',
            'ver panel de usuarios',
            'ver panel de solicitudes de reserva',
            'ver panel de reservas',
            'ver panel de préstamos',

            // — Ver cualquier —
            'ver cualquier reserva',
            'ver cualquier horario',
            'ver cualquier laboratorio',
            'ver cualquier producto',
            'ver cualquier rol',
            'ver cualquier usuario',
            'ver cualquier solicitud de reserva',
            'ver cualquier permiso',
            'ver cualquier préstamo',

            // — Ver propio —
            'ver mis préstamos',

            // — Actualizar —
            'actualizar horario',
            'actualizar laboratorio',
            'actualizar rol',
            'actualizar producto',
            'actualizar reserva',
            'actualizar usuario',
            'actualizar permiso',
            'actualizar préstamo',
            'actualizar solicitud de reserva',

            // — Crear —
            'crear permiso',
            'crear rol',
            'crear usuario',
            'crear horario',
            'crear reserva',
            'crear producto',
            'crear laboratorio',
            'crear solicitud de reserva',
            'crear préstamo',

            // — Eliminar —
            'eliminar rol',
            'eliminar reserva',
            'eliminar horario',
            'eliminar usuario',
            'eliminar permiso',
            'eliminar solicitud de reserva',
            'eliminar producto',
            'eliminar laboratorio',
            'eliminar préstamo',
        ];
        // 3. Registrar todos los permisos
        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name'       => $perm,
                'guard_name' => 'web',
            ]);
        }

        // 4. Asignación de permisos por rol

        // 4.1 ADMIN: todos los permisos
        $admin->syncPermissions(Permission::all());

        // 4.2 LABORATORISTA: igual que ADMIN pero *sin* crear horarios
        $labPerms = array_filter($permissions, function ($p) {
            return $p !== 'crear horario';
        });
        $laboratoryTechnician->syncPermissions($labPerms);

        // 4.3 DOCENTE: solo reservar espacios y ver su historial
        $teacher->syncPermissions([
            'ver panel de reservas',
            'ver panel de historial de reservas',
            'crear reserva',
        ]);

        // 4.4 ESTUDIANTE: idéntico a DOCENTE
        $student->syncPermissions([
            'ver panel de reservas',
            'ver panel de historial de reservas',
            'ver panel de préstamos',

        ]);

        $coordinator->syncPermissions([
            // Horarios
            'ver panel de horarios',
            'ver cualquier horario',
            'crear horario',
            'actualizar horario',
            'eliminar horario',

        ]);
    }
}
