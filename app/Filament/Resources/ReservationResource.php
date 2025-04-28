<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages\BookingCalendar;
use App\Models\Booking;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Booking Management';

    protected static ?string $pluralModelLabel = 'Bookings Management';
    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?string $recordTitleAttribute = 'user_name';

    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('status', 'pending')->count() > 0
            ? 'warning'
            : 'success';
    }

    public static function getNavigationBadgeTooltip(): string
    {
        return 'Pending reservations for review';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('view reservation panel') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => BookingCalendar::route('/'),
        ];
    }
}

