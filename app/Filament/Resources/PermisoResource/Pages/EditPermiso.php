<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Filament\Resources\PermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermiso extends EditRecord
{
    protected static string $resource = PermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
