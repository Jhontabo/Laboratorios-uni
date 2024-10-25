<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;

class CalendarReservas extends Page
{
    protected static string $resource = 'App\Filament\Resources\ReservaResource';

    // Ajusta la ruta de la vista a la ubicación correcta
    public function getView(): string
    {
        return 'filament.resources.reserva-resource.widgets.calendar-widget';  // Ruta corregida a la vista
    }
}
