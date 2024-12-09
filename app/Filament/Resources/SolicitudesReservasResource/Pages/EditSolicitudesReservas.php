<?php

namespace App\Filament\Resources\SolicitudesReservasResource\Pages;

use App\Filament\Resources\SolicitudesReservasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudesReservas extends EditRecord
{
    protected static string $resource = SolicitudesReservasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
