<?php

namespace App\Filament\Resources\ReservaResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Horario;
use Illuminate\Support\Carbon;

class CalendarWidget extends Widget
{
    protected static string $view = 'filament.resources.reserva-resource.widgets.calendar-widget';

    protected function getEvents(): array
    {
        $horarios = Horario::with('laboratorista', 'laboratorio')->get();
        $events = [];
        
        foreach ($horarios as $horario) {
            $fecha = Carbon::now();
            
            // Ajustamos la fecha al día de la semana del horario
            while (strtolower($fecha->locale('es')->dayName) !== strtolower($horario->dia_semana)) {
                $fecha->addDay();
            }

            $events[] = [
                'id' => $horario->id_horario,
                'title' => sprintf(
                    'Lab: %s - %s',
                    $horario->laboratorio->nombre ?? 'Sin nombre',
                    $horario->laboratorista->nombre ?? 'Sin nombre'
                ),
                'start' => $fecha->format('Y-m-d') . ' ' . $horario->hora_inicio,
                'end' => $fecha->format('Y-m-d') . ' ' . $horario->hora_fin,
                'backgroundColor' => '#' . substr(md5($horario->id_laboratorio), 0, 6),
            ];
        }

        return $events;
    }
}