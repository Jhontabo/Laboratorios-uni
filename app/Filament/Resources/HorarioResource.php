<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use Filament\Resources\Resource;


class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?string $navigationLabel = 'Administrar Horarios';
    protected static ?string $modelLabel = 'Horario';
    protected static ?string $pluralModelLabel = 'Horarios';
    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\Calendar::route('/'),
        ];
    }
}
