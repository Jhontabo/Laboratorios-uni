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
        // Crear laboratorio si no existe
        $laboratorio = Laboratorio::firstOrCreate(
            ['id_laboratorio' => 1],
            [
                'nombre' => 'Laboratorio Central',
                'ubicacion' => 'Edificio Principal, Piso 3',
                'capacidad' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Crear categorías necesarias
        $categorias = [
            ['id_categorias' => 1, 'nombre_categoria' => 'Electrónica'],
            ['id_categorias' => 2, 'nombre_categoria' => 'Óptica'],
            ['id_categorias' => 3, 'nombre_categoria' => 'Informática']
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['id_categorias' => $categoria['id_categorias']],
                $categoria
            );
        }

        // Datos de 10 productos diferentes
        $productos = [
            [
                'nombre' => 'Microscopio Digital',
                'descripcion' => 'Microscopio con cámara integrada 1000x',
                'cantidad_disponible' => 5,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 2, // Óptica
                'numero_serie' => 'MIC-001',
                'fecha_adquisicion' => Carbon::now()->subYear(),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 1200.50,
                'ubicacion' => 'Estante A1',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/microscopio.jpg'
            ],
            [
                'nombre' => 'Laptop HP EliteBook',
                'descripcion' => 'Laptop empresarial i7, 16GB RAM, 512GB SSD',
                'cantidad_disponible' => 8,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 3, // Informática
                'numero_serie' => 'LT-HP-001',
                'fecha_adquisicion' => Carbon::now()->subMonths(3),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 1500.00,
                'ubicacion' => 'Estante B2',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'usado',
                'imagen' => 'https://ejemplo.com/laptop.jpg'
            ],
            [
                'nombre' => 'Osciloscopio Digital',
                'descripcion' => 'Osciloscopio 100MHz, 2 canales',
                'cantidad_disponible' => 3,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 1, // Electrónica
                'numero_serie' => 'OSC-101',
                'fecha_adquisicion' => Carbon::now()->subYears(2),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 950.75,
                'ubicacion' => 'Estante C3',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'usado',
                'imagen' => 'https://ejemplo.com/osciloscopio.jpg'
            ],
            [
                'nombre' => 'Multímetro Digital',
                'descripcion' => 'Multímetro True RMS 6000 counts',
                'cantidad_disponible' => 12,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 1, // Electrónica
                'numero_serie' => 'MM-202',
                'fecha_adquisicion' => Carbon::now()->subMonths(8),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 120.00,
                'ubicacion' => 'Cajón D4',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/multimetro.jpg'
            ],
            [
                'nombre' => 'Fuente de Alimentación',
                'descripcion' => 'Fuente regulable 0-30V, 0-5A',
                'cantidad_disponible' => 6,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 1, // Electrónica
                'numero_serie' => 'FA-303',
                'fecha_adquisicion' => Carbon::now()->subYear(),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 250.00,
                'ubicacion' => 'Estante E5',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/fuente.jpg'
            ],
            [
                'nombre' => 'Cables HDMI',
                'descripcion' => 'Cables HDMI 2.0, 2 metros',
                'cantidad_disponible' => 25,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 3, // Informática
                'numero_serie' => 'CB-HDMI-01',
                'fecha_adquisicion' => Carbon::now()->subMonths(2),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 15.99,
                'ubicacion' => 'Cajón F6',
                'tipo_producto' => 'suministro',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/hdmi.jpg'
            ],
            [
                'nombre' => 'Kit de Componentes Electrónicos',
                'descripcion' => 'Kit con resistencias, capacitores y transistores',
                'cantidad_disponible' => 18,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 1, // Electrónica
                'numero_serie' => 'KT-ELEC-01',
                'fecha_adquisicion' => Carbon::now()->subMonths(4),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 45.50,
                'ubicacion' => 'Cajón G7',
                'tipo_producto' => 'suministro',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/componentes.jpg'
            ],
            [
                'nombre' => 'Proyector Epson',
                'descripcion' => 'Proyector Full HD 3500 lúmenes',
                'cantidad_disponible' => 2,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 3, // Informática
                'numero_serie' => 'PRO-EP-01',
                'fecha_adquisicion' => Carbon::now()->subMonths(5),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 800.00,
                'ubicacion' => 'Estante H8',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'usado',
                'imagen' => 'https://ejemplo.com/proyector.jpg'
            ],
            [
                'nombre' => 'Balanza Digital',
                'descripcion' => 'Balanza de precisión 0.01g, 500g max',
                'cantidad_disponible' => 4,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 2, // Óptica
                'numero_serie' => 'BAL-001',
                'fecha_adquisicion' => Carbon::now()->subMonths(7),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 180.25,
                'ubicacion' => 'Estante I9',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/balanza.jpg'
            ],
            [
                'nombre' => 'Termómetro Infrarrojo',
                'descripcion' => 'Termómetro digital con láser -50°C a 550°C',
                'cantidad_disponible' => 7,
                'id_laboratorio' => $laboratorio->id_laboratorio,
                'id_categorias' => 2, // Óptica
                'numero_serie' => 'TERM-IR-01',
                'fecha_adquisicion' => Carbon::now()->subMonths(9),
                'disponible_para_prestamo' => true,
                'costo_unitario' => 75.30,
                'ubicacion' => 'Cajón J10',
                'tipo_producto' => 'equipo',
                'estado_producto' => 'nuevo',
                'imagen' => 'https://ejemplo.com/termometro.jpg'
            ]
        ];

        // Crear los productos
        foreach ($productos as $productoData) {
            Producto::create($productoData);
        }

        $this->command->info('¡10 productos diferentes creados exitosamente!');
    }
}