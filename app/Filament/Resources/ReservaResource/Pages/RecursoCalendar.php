<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReservaResource;

use App\Filament\Widgets\ReservaCalendar;


use App\Models\Laboratorio;

class RecursoCalendar extends Page
{
    protected static string $resource = ReservaResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.reserva';

    public function getFooterWidgets(): array
    {
        // Obtén el parámetro 'widget' y establece 'Todos' como valor predeterminado
        $selectedWidget = request()->query('widget', 'Todos');

        // Si se selecciona "Todos", muestra el widget general de reservas
        if ($selectedWidget === 'Todos') {
            return [ReservaCalendar::class];
        }

        // Si el parámetro corresponde a un laboratorio específico, intenta cargarlo
        $laboratorio = Laboratorio::where('nombre', $selectedWidget)->first();

        if ($laboratorio) {
            return [ReservaCalendar::class]; // Puedes cambiarlo si tienes widgets específicos por laboratorio
        }

        return [ReservaCalendar::class]; // Retorna el widget por defecto
    }

    public function getDropdownOptions(): array
    {
        // Obtén todos los nombres de los laboratorios de la base de datos
        $laboratorios = Laboratorio::all()->pluck('nombre', 'nombre')->toArray();

        // Crea las opciones para el dropdown con "Todos"
        $options = ['Todos' => 'Todos'] + $laboratorios;

        return $options;
    }

    /**
     * Método para obtener la lista de laboratorios
     */
    public function getLaboratorios(): array
    {
        return Laboratorio::pluck('nombre', 'id_laboratorio')->toArray();
    }
}
