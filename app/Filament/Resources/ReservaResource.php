<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected  static ?string $navigationLabel = 'Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';
    public static function getPages(): array
    {
        return [
            'index' => Pages\CalendarioReserva::route('/')
        ];
    }
}
