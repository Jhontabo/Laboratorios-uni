<?php

namespace App\Filament\Widgets;

use App\Models\Reserva;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReservasEstadoDonutChart extends ChartWidget
{
    protected static ?string $heading = 'Reservas por Estado';
    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Reserva::query()
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->orderBy('total', 'desc')
            ->get();

        $totalReservas = $data->sum('total');

        return [
            'labels' => $data->pluck('estado')->map(fn($estado) => strtoupper($estado))->toArray(),
            'datasets' => [
                [
                    'label' => 'Total de Reservas',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => $this->generateColors($data->count()),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function generateColors(int $count): array
    {
        $stateColors = [
            'aceptada' => '#10b981',
            'rechazada' => '#ef4444',
            'pendiente' => '#f59e0b',
        ];

        $baseColors = [
            '#8b5cf6',
            '#06b6d4',
            '#d946ef',
            '#84cc16'
        ];

        $colors = [];
        $estados = Reserva::groupBy('estado')->pluck('estado')->toArray();

        foreach ($estados as $i => $estado) {
            $colors[] = $stateColors[strtolower($estado)] ?? $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }


    public function getDescription(): ?string
    {
        return 'Distribución porcentual de reservas por estado';
    }
}
