<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratorio;
use App\Models\Categoria;
use App\Models\Producto;
use Carbon\Carbon;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        // Primero asegurarnos de que exista al menos un laboratorio
        $laboratorio = Laboratorio::firstOrCreate([
            'id_laboratorio' => 1 // Usando el nombre correcto de la columna
        ], [
            'ubicacion' => 'Laboratorio Central',
            'nombre' => 'Lab Principal',
            'capacidad' => 30,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Asegurarnos de que exista al menos una categoría
        $categoria = Categoria::firstOrCreate([
            'id_categorias' => 1 // Nombre correcto según tu migración
        ], [
            'nombre_categoria' => 'Electrónica',

        ]);

        // Datos de productos de ejemplo
        $productos = [
            [
                'nombre' => 'Microscopio Digital',
                'descripcion' => 'Microscopio con cámara integrada 1000x',
                'cantidad_disponible' => 5,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => $categoria->id_categorias, // Usando el nombre correcto
                'numero_serie' => 'MIC-001',
                'fecha_adquisicion' => Carbon::now()->subYear(),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 1200.50,
                'ubicacion' => 'Estante A1',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'estado_prestamo' => 'disponible',
                'imagen' => 'https://ejemplo.com/microscopio.jpg'
            ],
            // ... más productos
        ];

        // Crear solo 10 productos
        for ($i = 0; $i < 10; $i++) {
            $productoData = $productos[$i % count($productos)];
            $productoData['numero_serie'] = $productoData['numero_serie'] . '-' . ($i + 1);

            Producto::create($productoData);
        }

        $this->command->info('¡10 productos de prueba creados exitosamente!');
    }
}
