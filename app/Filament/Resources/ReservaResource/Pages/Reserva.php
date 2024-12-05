<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use App\Filament\Widgets\CalendarReserva;
use Filament\Resources\Pages\Page;

class Reserva extends Page
{
    protected static string $resource = ReservaResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.reserva';
}
