<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Resources\Resource;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Gestion de Reservas';
    protected static ?string $navigationLabel = 'Administrar Horarios';
    protected static ?string $modelLabel = 'Horario';
    protected static ?string $pluralLabel = 'Horarios';





    public static function getPages(): array
    {
        return [
            'index' => Pages\ScheduleCalendar::route('/'),
        ];
    }
}
