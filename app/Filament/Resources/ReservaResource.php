<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages\RecursoCalendar;
use App\Models\Horario;
use App\Models\Reserva;
use Filament\Resources\Resource;


class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';

    public static function getPages(): array
    {
        return [
            'index' => RecursoCalendar::route('/'), // Usar la clase correcta
        ];
    }
}
