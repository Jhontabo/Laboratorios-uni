<?php

namespace App\Filament\Resources\SolicitudesReservasResource\Pages;

use App\Filament\Resources\SolicitudesReservasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudesReservas extends ListRecords
{
    protected static string $resource = SolicitudesReservasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
