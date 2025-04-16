<?php

namespace App\Filament\Widgets;

use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Producto;
use App\Models\Reserva;
use App\Models\User;
use DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected ?string $heading = 'Resumen de estadísticas';

    protected ?string $description = 'Metricas clave del sistema';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {


        return [
            Stat::make('Productos registrados', Producto::count())
                ->description('Productos en inventario')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary'),

            Stat::make('Categorias activas', Categoria::count())
                ->description('Organización de productos')
                ->descriptionIcon('heroicon-o-tag')
                ->color('danger'),
            Stat::make('Laboratorios', Laboratorio::count())
                ->description('Espacios disponibles')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('warning'),

            Stat::make('Usuarios activos', User::where('estado', true)->count())
                ->description('Total: ' . User::count())
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),



            Stat::make('Total reservas', Reserva::count())
                ->description('Total de reservas en el sistema')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary'),
        ];
    }
}
