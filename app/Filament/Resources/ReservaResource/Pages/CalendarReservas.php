<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;
use App\Models\Reserva; // Asegúrate de importar el modelo correcto

class CalendarReservas extends Page
{
    protected static string $resource = 'App\Filament\Resources\ReservaResource';
    protected static string $view = 'filament.resources.reserva-resource.widgets.calendar-widget';

    // Método getEvents
    public function getEvents(): array
    {
        // Aquí puedes ajustar la consulta a tu modelo de reservas
        $reservas = Reserva::all(); // Ajusta según tus necesidades
        $events = [];

        foreach ($reservas as $reserva) {
            $events[] = [
                'title' => $reserva->titulo, // Asegúrate de que estos campos existan en tu modelo
                'start' => $reserva->fecha_inicio, // Asegúrate de que estas columnas existan en tu tabla
                'end' => $reserva->fecha_fin,
            ];
        }

        return $events;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\ReservaResource\Widgets\CalendarWidget::class,
        ];
    }

    public function getMaxContentWidth(): string
    {
        return '6xl';
    }
}
