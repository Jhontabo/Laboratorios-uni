<?php

namespace App\Filament\Resources\HistorialReservasResource\Pages;

use App\Models\Reserva;
use Filament\Resources\Pages\ViewRecord;

class ViewHistorialReserva extends ViewRecord
{
    protected static string $resource = \App\Filament\Resources\HistorialReservasResource::class;

    protected static string $view = 'filament.resources.historial-reservas.pages.view';
}
