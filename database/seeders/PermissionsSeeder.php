<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin                = Role::firstOrCreate(['name' => 'ADMIN',         'guard_name' => 'web']);
        $laboratory           = Role::firstOrCreate(['name' => 'LABORATORISTA', 'guard_name' => 'web']);
        $teacher              = Role::firstOrCreate(['name' => 'DOCENTE',       'guard_name' => 'web']);
        $student              = Role::firstOrCreate(['name' => 'ESTUDIANTE',    'guard_name' => 'web']);
        $coordinator          = Role::firstOrCreate(['name' => 'COORDINADOR',   'guard_name' => 'web']);


        $permissions = [
            'ver panel solicitudes prestamo',
            'ver panel administrar prestamos',
            'ver panel mis prestamos',
            'ver panel reservar espacios',
            'ver panel historial reservas',
            'ver panel solicitud reservas',
            'ver panel horarios',
            'ver panel laboratorios',
            'ver panel inventario',
            'ver panel roles',
            'ver panel usuarios',

            // — Ver cualquier —
            'ver cualquier solicitud prestamo',
            'ver cualquier administrar prestamo',
            'ver cualquier mis prestamo',
            'ver cualquier reservar espacio',
            'ver cualquier historial reserva',
            'ver cualquier solicitud reserva',
            'ver cualquier horario',
            'ver cualquier laboratorio',
            'ver cualquier inventario',
            'ver cualquier rol',
            'ver cualquier usuario',



            // — Actualizar —
            'actualizar solicitud prestamo',
            'actualizar administrar prestamo',
            'actualizar mis prestamo',
            'actualizar reservar espacio',
            'actualizar historial reserva',
            'actualizar solicitud reserva',
            'actualizar horario',
            'actualizar laboratorio',
            'actualizar inventario',
            'actualizar rol',
            'actualizar usuario',

            // — Crear —
            'crear solicitud prestamo',
            'crear administrar prestamo',
            'crear mis prestamo',
            'crear reservar espacio',
            'crear historial reserva',
            'crear solicitud reserva',
            'crear horario',
            'crear laboratorio',
            'crear inventario',
            'crear rol',
            'crear usuario',

            // — Eliminar —
            'eliminar solicitud prestamo',
            'eliminar administrar prestamo',
            'eliminar mis prestamo',
            'eliminar reservar espacio',
            'eliminar historial reserva',
            'eliminar solicitud reserva',
            'eliminar horario',
            'eliminar laboratorio',
            'eliminar inventario',
            'eliminar rol',
            'eliminar usuario',

        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name'       => $perm,
                'guard_name' => 'web',
            ]);
        }


        $admin->syncPermissions(Permission::all());

        $laboratory->syncPermissions([]);
        $teacher->syncPermissions([]);

        $student->syncPermissions([
            'ver panel solicitudes prestamo',
            'ver panel administrar prestamos',
            'ver panel mis prestamos',
            'ver panel reservar espacios',
            'ver panel historial reservas',
            'ver panel solicitud reservas',

            // — Ver cualquier —
            'ver cualquier solicitud prestamo',
            'ver cualquier administrar prestamo',
            'ver cualquier mis prestamo',
            'ver cualquier reservar espacio',
            'ver cualquier historial reserva',
            'ver cualquier solicitud reserva',



            // — Actualizar —
            'actualizar solicitud prestamo',
            'actualizar administrar prestamo',
            'actualizar mis prestamo',
            'actualizar reservar espacio',
            'actualizar historial reserva',
            'actualizar solicitud reserva',

            // — Crear —
            'crear solicitud prestamo',
            'crear administrar prestamo',
            'crear mis prestamo',
            'crear reservar espacio',
            'crear historial reserva',
            'crear solicitud reserva',

            // — Eliminar —
            'eliminar solicitud prestamo',
            'eliminar administrar prestamo',
            'eliminar mis prestamo',
            'eliminar reservar espacio',
            'eliminar historial reserva',
            'eliminar solicitud reserva',
        ]);

        $coordinator->syncPermissions([]);
    }
}
