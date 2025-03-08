<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Permisos extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $roles = ['ADMIN', 'LABORATORISTA', 'DOCENTE', 'ESTUDIANTE'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear permisos
        $permissions = [
            // Permisos de Categoría
            'ver cualquier categoria',
            'ver categoria',
            'crear categoria',
            'actualizar categoria',
            'eliminar categoria',
            'restaurar categoria',
            'eliminar permanentemente categoria',

            // Permisos de Laboratorio
            'ver cualquier laboratorio',
            'ver laboratorio',
            'crear laboratorio',
            'actualizar laboratorio',
            'eliminar laboratorio',
            'restaurar laboratorio',
            'eliminar permanentemente laboratorio',

            // Permisos de Horario
            'ver cualquier horario',
            'ver horario',
            'crear horario',
            'actualizar horario',
            'eliminar horario',
            'restaurar horario',
            'eliminar permanentemente horario',

            // Permisos de Producto
            'ver cualquier producto',
            'ver producto',
            'crear producto',
            'actualizar producto',
            'eliminar producto',
            'restaurar producto',
            'eliminar permanentemente producto',

            // Permisos de Reserva
            'ver cualquier reserva',
            'ver reserva',
            'crear reserva',
            'actualizar reserva',
            'eliminar reserva',
            'restaurar reserva',
            'eliminar permanentemente reserva',

            // Permisos de Rol
            'ver cualquier rol',
            'ver rol',
            'crear rol',
            'actualizar rol',
            'eliminar rol',

            // Permisos de Usuario
            'ver cualquier usuario',
            'ver usuario',
            'crear usuario',
            'actualizar usuario',
            'eliminar usuario',

            // Permisos de Solicitud de Reserva
            'ver cualquier solicitud reserva',
            'ver solicitud reserva',
            'crear solicitud reserva',
            'actualizar solicitud reserva',
            'eliminar solicitud reserva',
            'restaurar solicitud reserva',
            'eliminar permanentemente solicitud reserva',

            // Permisos de Administrador
            'asignar permisos',
            'asignar roles',
            'ver logs',
            'configurar sistema',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Obtener roles
        $admin = Role::where('name', 'ADMIN')->first();
        $laboratorista = Role::where('name', 'LABORATORISTA')->first();
        $docente = Role::where('name', 'DOCENTE')->first();
        $estudiante = Role::where('name', 'ESTUDIANTE')->first();

        // Asignar permisos a los roles
        $admin->syncPermissions(Permission::all()); // ADMIN tiene todos los permisos
        $laboratorista->syncPermissions([
            'ver cualquier categoria',
            'ver categoria',
            'ver cualquier horario',
            'ver horario'
        ]);
        $docente->syncPermissions([
            'ver cualquier horario',
            'ver horario'
        ]);
        $estudiante->syncPermissions([
            'ver cualquier producto',
            'ver producto'
        ]);

        $this->command->info('Roles y permisos creados correctamente.');
    }
}
