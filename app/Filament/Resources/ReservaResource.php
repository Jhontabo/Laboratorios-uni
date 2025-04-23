<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages\RecursoCalendar;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Gestión de Reservas';
    protected static ?string $modelLabel = 'Reserva';
    protected static ?string $pluralModelLabel = 'Gestión de Reservas';
    protected static ?string $navigationGroup = 'Gestión Académica';


    protected static ?string $recordTitleAttribute = 'nombre_usuario';



    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('estado', 'pendiente')->count() > 0
            ? 'warning'
            : 'success';
    }

    public static function getNavigationBadgeTooltip(): string
    {
        return 'Reservas pendientes de revisión';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('ver panel reservas') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => RecursoCalendar::route('/'),
        ];
    }
}
