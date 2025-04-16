<?php

namespace App\Filament\Widgets;

use App\Models\Reserva;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TendenciasReservasAreaChart extends ChartWidget
{
    protected static ?string $heading = 'Tendencias Mensuales de Reservas';
    protected static ?string $maxHeight = '350px';
    protected static ?int $sort = 4;

    // Eliminamos la propiedad timeRange ya que siempre mostraremos el mes actual
    // public ?string $timeRange = 'current_month';

    protected function getData(): array
    {
        $query = Reserva::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [
                now()->startOfMonth(), // Fecha inicial: inicio del mes actual
                now()->endOfMonth()    // Fecha final: fin del mes actual
            ])
            ->groupBy('date')
            ->orderBy('date');

        $data = $query->get();

        return [
            'labels' => $data->pluck('date')->map(fn($date) => $this->formatDate($date))->toArray(),
            'datasets' => [
                [
                    'label' => 'Reservas',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }



    protected function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('d M');
    }

    public function getDescription(): ?string
    {
        return "Tendencia diaria de reservas para el mes actual";
    }
}
