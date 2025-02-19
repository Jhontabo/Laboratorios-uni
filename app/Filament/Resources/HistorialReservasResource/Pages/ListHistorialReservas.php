<?php

namespace App\Filament\Resources\HistorialReservasResource\Pages;

use App\Filament\Resources\HistorialReservasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorialReservas extends ListRecords
{
    protected static string $resource = HistorialReservasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
