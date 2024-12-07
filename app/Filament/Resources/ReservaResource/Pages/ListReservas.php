<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\HorarioResource;
use App\Filament\Widgets\CalendarReserva;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservas extends ListRecords
{
    protected static string $resource = HorarioResource::class;
}
