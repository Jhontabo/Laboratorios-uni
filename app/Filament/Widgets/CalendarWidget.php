<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;


class CalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario de Reservas';

    public static function canView(): bool
    {
        // Ocultar el widget en el dashboard principal
        return !request()->routeIs('filament.admin.pages.dashboard');
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Horario::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(function (Horario $horario) {
                return [
                    'id' => $horario->id_horario,
                    'title' => $horario->title,
                    'start' => $horario->start_at,
                    'end' => $horario->end_at,
                    'color' => $horario->color,
                ];
            })
            ->toArray();
    }
}
