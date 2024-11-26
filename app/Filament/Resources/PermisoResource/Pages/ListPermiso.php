<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Filament\Resources\PermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermisos extends ListRecords
{
    protected static string $resource = PermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
