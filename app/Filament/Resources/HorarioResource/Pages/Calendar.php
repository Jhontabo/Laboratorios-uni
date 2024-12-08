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

    public function getFooterWidgets(): array
    {
        // Obtén el parámetro 'widget' y establece 'Todos' como valor predeterminado
        $selectedWidget = request()->query('widget', 'Todos');

        // Si se selecciona "Todos", muestra un widget general
        if ($selectedWidget === 'Todos') {
            return [CalendarWidget::class];
        }

        // Si el parámetro corresponde a un laboratorio específico, intenta cargarlo
        $laboratorio = Laboratorio::where('nombre', $selectedWidget)->first();

        if ($laboratorio) {
            // Devuelve un widget específico relacionado con el laboratorio
            return [CalendarWidget::class]; // Personaliza este widget si es necesario
        }

        if ($selectedWidget === 'Reserva') {
            return [CalendarReserva::class];
        }

        // Retorna un widget por defecto si no se encuentra un laboratorio
        return [CalendarWidget::class];
    }


    public function getDropdownOptions(): array
    {
        // Obtén todos los nombres de los laboratorios de la base de datos
        $laboratorios = Laboratorio::all()->pluck('nombre', 'nombre')->toArray();

        // Crea las opciones para el dropdown con "Todos" y "Reserva"
        $options = [
            'Todos' => 'Todos',
            'Reserva' => 'Reserva'
        ] + $laboratorios;


        return $options;
    }
}
