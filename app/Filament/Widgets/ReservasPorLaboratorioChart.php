<?php

namespace App\Filament\Widgets;

use App\Models\Laboratorio;
use DB;
use Filament\Widgets\ChartWidget;

class ReservasPorLaboratorioChart extends ChartWidget
{
    protected static ?string $heading = 'Reservas por Laboratorio';
    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 3;

    public ?int $id_laboratorio = null;

    protected function getData(): array
    {
        // Obtener el laboratorio de la sesión si está disponible
        $this->id_laboratorio = session()->get('lab');

        // Consulta para contar reservas por laboratorio
        $query = Laboratorio::query()
            ->leftJoin('reservas', 'laboratorios.id_laboratorio', '=', 'reservas.id_laboratorio')
            ->select(
                'laboratorios.nombre',
                DB::raw('COUNT(reservas.id_reserva) as total_reservas')
            )
            ->groupBy('laboratorios.id_laboratorio', 'laboratorios.nombre')
            ->orderBy('total_reservas', 'desc');

        // Filtrar por laboratorio si está especificado
        if ($this->id_laboratorio) {
            $query->where('laboratorios.id_laboratorio', $this->id_laboratorio);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('nombre')->toArray(),
            'datasets' => [
                [
                    'label' => 'Total de Reservas',
                    'data' => $data->pluck('total_reservas')->toArray(),
                    'backgroundColor' => $this->generateColors($data->count()),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return $this->id_laboratorio ? 'bar' : 'pie';
    }

    protected function generateColors(int $count): array
    {
        $colors = [];
        $baseColors = [
            '#4f46e5',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#06b6d4',
            '#d946ef',
            '#84cc16'
        ];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }

    public function getDescription(): ?string
    {
        return $this->id_laboratorio
            ? 'Reservas para este laboratorio'
            : 'Distribución de reservas por laboratorio';
    }
}
