<?php

namespace App\Filament\Resources\HorarioResource\Pages;

use App\Filament\Resources\HorarioResource;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\AnotherWidget; // Incluye otros widgets si es necesario
use App\Filament\Widgets\CalendarReserva;

class Calendar extends Page
{
    protected static string $resource = HorarioResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.calendar';

    protected function getFooterWidgets(): array
    {
        $selectedWidget = request()->query('widget'); // Obtén el parámetro 'widget'

        if ($selectedWidget === 'Horario') {
            return [CalendarWidget::class];
        }

        if ($selectedWidget === 'Reserva') {
            return [CalendarReserva::class];
        }

        // No retorna widgets si no hay selección válida
        return [];
    }

    public function getDropdownOptions(): array
    {
        return [
            'Horario' => 'Horario',
            'Reserva' => 'Reserva',
        ];
    }
}
