<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages\ReservaCalendar; // Importación correcta
use App\Filament\Widgets\ReservaCalendar as WidgetReservaCalendar; // Evitar confusión con nombres
use App\Models\Horario;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';

    public static function getPages(): array
    {
        return [
            'index' => ReservaCalendar::route('/'), // Usar la clase correcta
        ];
    }
    
}