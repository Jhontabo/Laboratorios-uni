<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Resources\Resource;

class ScheduleResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Academic Management';
    protected static ?string $navigationLabel = 'Manage Schedules';
    protected static ?string $modelLabel = 'Schedule';
    protected static ?string $pluralModelLabel = 'Schedules';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ScheduleCalendar::route('/'),
        ];
    }
}

