<?php

namespace App\Filament\Resources\HistorialReservasResource\Pages;

use App\Filament\Resources\HistorialReservasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorialReservas extends EditRecord
{
    protected static string $resource = HistorialReservasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
