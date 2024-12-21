<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Imports\UsuariosImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear Usuario'),
            ExcelImportAction::make()
                ->label('Importar Usuarios')
                ->use(UsuariosImport::class)
                ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain']),

        ];
    }
}
