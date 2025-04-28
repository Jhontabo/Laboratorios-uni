<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'ADMIN', 'guard_name' => 'web']);
        $laboratoryTechnician = Role::firstOrCreate(['name' => 'LABORATORISTA', 'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'DOCENTE', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'ESTUDIANTE', 'guard_name' => 'web']);

        // List of permissions (in English now)
        $permissions = [
            // Panels
            'view category panel',
            'view any category',
            'view product panel',
            'view reservation history panel',
            'view schedule panel',
            'view laboratory panel',
            'view permission panel',
            'view any reservation',
            'view any schedule',
            'view any laboratory',
            'view any product',
            'view any role',
            'view any user',
            'view role panel',
            'view user panel',
            'view reservation requests panel',
            'view any reservation request',
            'view any permission',
            'view permissions',
            'view role',
            'view user',
            'view reservation request',
            'view schedule',
            'view laboratory',
            'view category',
            'view product',
            'view reservation panel',

            // Update
            'update schedule',
            'update laboratory',
            'update category',
            'update role',
            'update product',
            'update reservation',
            'update user',

            // Create
            'create permission',
            'create role',
            'create user',
            'create category',
            'create schedule',
            'create reservation',
            'create product',
            'create laboratory',
            'create reservation request',

            // Delete
            'delete role',
            'delete reservation',
            'delete schedule',
            'delete category',
            'delete user',
            'delete permission',
            'delete reservation request',
            'delete product',
            'delete laboratory',
        ];

        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to ADMIN
        $admin->syncPermissions(Permission::all());

        // Assign specific permissions to LABORATORISTA
        $laboratoryTechnician->syncPermissions([
            'view category panel',
            'view any category',
            'view product panel',
            'view reservation history panel',
            'view schedule panel',
            'view laboratory panel',
            'view permission panel',
            'view any reservation',
            'view any schedule',
            'view any laboratory',
            'view any product',
            'view any role',
            'view any user',
            'view role panel',
            'view user panel',
            'view reservation requests panel',
            'view any reservation request',
            'view any permission',
            'view permissions',
            'view role',
            'view user',
            'view reservation request',
            'view schedule',
            'view laboratory',
            'view category',
            'view product',
            'view reservation panel',

            'update schedule',
            'update laboratory',
            'update category',
            'update role',
            'update product',
            'update reservation',
            'update user',

            'create permission',
            'create role',
            'create user',
            'create category',
            'create schedule',
            'create reservation',
            'create product',
            'create laboratory',
            'create reservation request',

            'delete role',
            'delete reservation',
            'delete schedule',
            'delete category',
            'delete user',
            'delete permission',
            'delete reservation request',
            'delete product',
            'delete laboratory',
        ]);

        // Assign limited permissions to DOCENTE
        $teacher->syncPermissions([
            'view reservation history panel',
            'view laboratory panel',
            'view any reservation request',
            'view reservation panel',
            'create reservation request',
        ]);

        // Assign limited permissions to ESTUDIANTE
        $student->syncPermissions([
            'view reservation history panel',
            'view laboratory panel',
            'view any reservation request',
            'view reservation panel',
            'create reservation request',
        ]);
    }
}

