<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insertar usuarios
        $usuarioJhonId = DB::table('users')->insertGetId([
            'nombre' => 'Jhon',
            'apellido' => 'Tajumbina',
            'correo_electronico' => 'jhonse.tajumbina@umariana.edu.co',
            'telefono' => '123456789',
            'Direccion' => 'Calle Falsa 123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $usuarioJonathanId = DB::table('users')->insertGetId([
            'nombre' => 'Jonathan',
            'apellido' => 'Burbano',
            'correo_electronico' => 'jonathanc.burbano221@umariana.edu.co',
            'telefono' => '987654321',
            'Direccion' => 'Avenida Siempreviva 456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar laboratoristas asociados a los usuarios
        $laboratoristaJhonId = DB::table('laboratorista')->insertGetId([
            'id_usuario' => $usuarioJhonId,
            'estado' => 'Activo',
            'fecha_ingreso' => Carbon::now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $laboratoristaJonathanId = DB::table('laboratorista')->insertGetId([
            'id_usuario' => $usuarioJonathanId,
            'estado' => 'Activo',
            'fecha_ingreso' => Carbon::now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar laboratorios
        $laboratorioAId = DB::table('laboratorio')->insertGetId([
            'nombre' => 'Laboratorio A',
            'ubicacion' => 'Edificio 1, Piso 2',
            'capacidad' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $laboratorioBId = DB::table('laboratorio')->insertGetId([
            'nombre' => 'Laboratorio B',
            'ubicacion' => 'Edificio 1, Piso 3',
            'capacidad' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar horarios
        DB::table('horario')->insert([
            [
                'id_laboratorista' => $laboratoristaJhonId,
                'id_laboratorio' => $laboratorioAId,
                'dia_semana' => 'Lunes',
                'hora_inicio' => '08:00',
                'hora_fin' => '10:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_laboratorista' => $laboratoristaJonathanId,
                'id_laboratorio' => $laboratorioBId,
                'dia_semana' => 'Martes',
                'hora_inicio' => '14:00',
                'hora_fin' => '16:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_laboratorista' => $laboratoristaJhonId,
                'id_laboratorio' => $laboratorioBId,
                'dia_semana' => 'Miércoles',
                'hora_inicio' => '10:00',
                'hora_fin' => '12:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insertar categorías
        $consumiblesId = DB::table('categorias')->insertGetId([
            'nombre_categoria' => 'Consumibles',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $noConsumiblesId = DB::table('categorias')->insertGetId([
            'nombre_categoria' => 'No consumibles',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar productos asociados a las categorías
        DB::table('productos')->insert([
            [
                'nombre' => 'Reactivo Químico',
                'descripcion' => 'Reactivo para experimentos de laboratorio.',
                'cantidad_disponible' => 100,
                'id_laboratorio' => $laboratorioAId,
                'id_categorias' => $consumiblesId,
                'numero_serie' => 'RQ-2024-001',
                'ubicacion' => 'Almacén 1',
                'estado' => 'nuevo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Microscopio',
                'descripcion' => 'Microscopio de alta precisión.',
                'cantidad_disponible' => 10,
                'id_laboratorio' => $laboratorioBId,
                'id_categorias' => $noConsumiblesId,
                'numero_serie' => 'MS-2024-002',
                'ubicacion' => 'Laboratorio de Biología',
                'estado' => 'usado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Ejecución de otros seeders si es necesario
        $this->call([
            UsersSeeder::class,
        ]);
    }
}
