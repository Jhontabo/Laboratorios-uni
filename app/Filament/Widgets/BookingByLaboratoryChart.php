<?php

namespace App\Filament\Widgets;

use App\Models\Laboratory;
use DB;
use Filament\Widgets\ChartWidget;

class BookingByLaboratoryChart extends ChartWidget
{
    protected static ?string $heading = 'Bookings by Laboratory';
    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 3;

    public ?int $laboratoryId = null;

    protected function getData(): array
    {
        // Get the laboratory from session if available
        $this->laboratoryId = session()->get('lab');

        // Query to count bookings per laboratory
        $query = Laboratory::query()
            ->leftJoin('bookings', 'laboratories.id_laboratory', '=', 'bookings.id_laboratory')
            ->select(
                'laboratories.name',
                DB::raw('COUNT(bookings.id_booking) as total_bookings')
            )
            ->groupBy('laboratories.id_laboratory', 'laboratories.name')
            ->orderBy('total_bookings', 'desc');

        // Filter by laboratory if specified
        if ($this->laboratoryId) {
            $query->where('laboratories.id_laboratory', $this->laboratoryId);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Bookings',
                    'data' => $data->pluck('total_bookings')->toArray(),
                    'backgroundColor' => $this->generateColors($data->count()),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
            '#84cc16',
        ];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }

    public function getDescription(): ?string
    {
        return 'Distribution of bookings by laboratory';
    }
}

