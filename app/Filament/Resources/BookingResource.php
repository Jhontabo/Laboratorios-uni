<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages\BookingResourceCalendar;
use App\Models\Booking;
use Filament\Resources\Resource;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Reservar Espacio';
    protected static ?string $navigationGroup = 'Gestion de Reservas';



    public static function getPages(): array
    {
        return [
            'index' => BookingResourceCalendar::route('/'),
        ];
    }
}
