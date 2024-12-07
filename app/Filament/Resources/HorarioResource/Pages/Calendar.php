<?php

namespace App\Filament\Resources\HorarioResource\Pages;

use App\Filament\Resources\HorarioResource;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\CalendarReserva;
use App\Models\Laboratorio; // Modelo del laboratorio

class Calendar extends Page
{
    protected static string $resource = HorarioResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.calendar';

    protected function getFooterWidgets(): array
    {
        // Obtén el parámetro 'widget' y establece 'Todos' como valor predeterminado
        $selectedWidget = request()->query('widget', 'Todos');

        // Consulta el laboratorio seleccionado si coincide con el nombre
        $laboratorio = Laboratorio::where('nombre', $selectedWidget)->first();

        if ($laboratorio) {
            // Devuelve un widget específico relacionado con el laboratorio
            return [CalendarWidget::class]; // Puedes personalizar este widget si es necesario
        }

        if ($selectedWidget === 'Reserva') {
            return [CalendarReserva::class];
        }

        // No retorna widgets si no hay selección válida
        return [];
    }

    public function getDropdownOptions(): array
    {
        // Obtén todos los nombres de los laboratorios de la base de datos
        $laboratorios = Laboratorio::all()->pluck('nombre')->toArray();

        // Crea las opciones para el dropdown
        $options = ['Todos' => 'Todos', 'Reserva' => 'Reserva'];

        foreach ($laboratorios as $laboratorio) {
            $options[$laboratorio] = $laboratorio;
        }

        return $options;
    }
}
