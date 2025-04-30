<?php

namespace App\Filament\Widgets;

use App\Models\Laboratory;
use App\Models\Product;
use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Statistics Overview';
    protected ?string $description = 'Key system metrics';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Registered Products', Product::count())
                ->description('Products in inventory')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary'),



            Stat::make('Laboratories', Laboratory::count())
                ->description('Available spaces')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('warning'),

            Stat::make('Active Users', User::where('status', true)->count())
                ->description('Total: ' . User::count())
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Total Bookings', Booking::count())
                ->description('Total bookings in the system')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary'),
        ];
    }
}
