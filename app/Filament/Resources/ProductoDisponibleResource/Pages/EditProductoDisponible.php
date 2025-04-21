<?php

namespace App\Filament\Resources\ProductoDisponibleResource\Pages;

use App\Filament\Resources\ProductoDisponibleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductoDisponible extends EditRecord
{
    protected static string $resource = ProductoDisponibleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
