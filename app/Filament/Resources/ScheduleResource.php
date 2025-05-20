<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Gestion de Reservas';
    protected static ?string $navigationLabel = 'Crear Horarios';



    public static function getPages(): array
    {
        return [

            'index' => Pages\ScheduleCalendar::route('/'),
        ];
    }
}
