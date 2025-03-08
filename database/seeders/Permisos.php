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
        $admin = Role::firstOrCreate(['name' => 'ADMIN', 'guard_name' => 'web']);
        $laboratorista = Role::firstOrCreate(['name' => 'LABORATORISTA', 'guard_name' => 'web']);
        $docente = Role::firstOrCreate(['name' => 'DOCENTE', 'guard_name' => 'web']);
        $estudiante = Role::firstOrCreate(['name' => 'ESTUDIANTE', 'guard_name' => 'web']);

        // Lista de permisos
        $permissions = [
            'ver panel categorias',
            'ver cualquier categoria', // Agregado el permiso faltante
            'crear categoria',
            'actualizar categoria',
            'eliminar categoria',
            'ver panel historial reservas',
            'ver panel horarios',
            'crear horario',
            'actualizar horario',
            'eliminar horario',
            'ver panel laboratorios',
            'crear laboratorio',
            'actualizar laboratorio',
            'eliminar laboratorio',
            'ver panel permisos',
            'asignar permisos',
            'ver panel productos',
            'crear producto',
            'actualizar producto',
            'eliminar producto',
            'ver panel reservas',
            'crear reserva',
            'actualizar reserva',
            'ver cualquier reserva',
            'ver cualquier horario',
            'ver cualquier laboratorio',
            'ver cualquier producto',
            'ver cualquier rol',
            'ver cualquier usuario',
            'eliminar reserva',
            'ver panel roles',
            'ver panel usuarios',
            'crear rol',
            'actualizar rol',
            'eliminar rol',
            'ver panel solicitudes reservas'
        ];

        // Crear los permisos en la base de datos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Asignar TODOS los permisos al rol ADMIN
        $admin->syncPermissions(Permission::all());
    }
}
