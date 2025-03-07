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
            'ver panel categorias', 'crear categoria', 'actualizar categoria', 'eliminar categoria',
            'ver panel historial reservas',
            'ver panel horarios', 'crear horario', 'actualizar horario', 'eliminar horario',
            'ver panel laboratorios', 'crear laboratorio', 'actualizar laboratorio', 'eliminar laboratorio',
            'ver panel permisos', 'asignar permisos',
            'ver panel productos', 'crear producto', 'actualizar producto', 'eliminar producto',
            'ver panel reservas', 'crear reserva', 'actualizar reserva', 'eliminar reserva',
            'ver panel roles', 'crear rol', 'actualizar rol', 'eliminar rol',
            'ver panel solicitudes reservas', 'crear solicitud reserva', 'actualizar solicitud reserva', 'eliminar solicitud reserva',
            'ver panel usuarios', 'crear usuario', 'actualizar usuario', 'eliminar usuario'
        ];
        
        // Crear los permisos en la base de datos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Asignar TODOS los permisos al rol ADMIN
        $admin->syncPermissions(Permission::all());
    }
}