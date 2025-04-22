<?php

namespace App\Filament\Resources\GestionPrestamoResource\Pages;

use App\Filament\Resources\GestionPrestamoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestionPrestamos extends ListRecords
{
    protected static string $resource = GestionPrestamoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
