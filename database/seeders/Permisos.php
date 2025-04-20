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

            #paneles
            'ver panel dashboard',
            'ver panel categorias',
            'ver cualquier categoria', // Agregado el permiso faltante
            'ver panel productos',
            'ver panel historial reservas',
            'ver panel horarios',
            'ver panel laboratorios',
            'ver panel permisos',
            'ver cualquier reserva',
            'ver cualquier horario',
            'ver cualquier laboratorio',
            'ver cualquier producto',
            'ver cualquier rol',
            'ver cualquier usuario',
            'ver panel roles',
            'ver panel usuarios',
            'ver panel solicitudes reservas',
            'ver cualquier solicitud reserva',
            'ver cualquier permiso',
            'ver permisos',
            'ver rol',
            'ver usuario',
            'ver solicitud reserva',
            'ver horario',
            'ver laboratorio',
            'ver categoria',
            'ver producto',
            'ver panel reservas',


            'actualizar horario',
            'actualizar laboratorio',
            'actualizar categoria',
            'actualizar rol',
            'actualizar producto',
            'actualizar reserva',
            'actualizar usuario',



            'crear permisos',
            'crear rol',
            'crear usuario',
            'crear categoria',
            'crear horario',
            'crear reserva',
            'crear producto',
            'crear laboratorio',
            'crear solicitud reserva',


            'eliminar rol',
            'eliminar reserva',
            'eliminar horario',
            'eliminar categoria',
            'eliminar usuario',
            'eliminar permisos',
            'eliminar solicitud reserva',
            'eliminar producto',
            'eliminar laboratorio',
        ];

        // Crear los permisos en la base de datos
        foreach (array_merge($permissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Asignar TODOS los permisos al rol ADMIN
        $admin
            ->syncPermissions(Permission::all());     // paneles

    }
}
