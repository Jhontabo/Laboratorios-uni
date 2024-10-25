<?php

namespace App\Filament\Resources\ReservaResource\Widgets;

use App\Models\Reserva;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class ReservaCalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        // Obtener las reservas dentro del rango de fechas proporcionado por FullCalendar
        return Reserva::query()
            ->whereHas('horario', function ($query) use ($fetchInfo) {
                $query->where('hora_inicio', '>=', $fetchInfo['start'])
                      ->where('hora_fin', '<=', $fetchInfo['end']);
            })
            ->get()
            ->map(function (Reserva $reserva) {
                return [
                    'title' => 'Reserva: ' . $reserva->laboratorio->nombre,
                    'start' => $reserva->horario->hora_inicio,
                    'end' => $reserva->horario->hora_fin,
                    'url' => \App\Filament\Resources\ReservaResource::getUrl('create'),
                    'shouldOpenUrlInNewTab' => true,
                ];
            })
            ->all();
    }
}
