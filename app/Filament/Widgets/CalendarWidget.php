<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Filament\Resources\HorarioResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;


class CalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario de Reservas';

    // Modelo para el widget
    public Model | string | null $model = Horario::class;

    // Método para decidir si el widget debe ser visible
    public static function canView(): bool
    {
        // Ocultar el widget en el dashboard principal
        return !request()->routeIs('filament.admin.pages.dashboard');
    }

    // Configuración de FullCalendar
    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridWeek,dayGridDay',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
        ];
    }

    // Método para obtener eventos de la base de datos
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
                    'color' => $horario->color, // Se asume que el modelo tiene esta propiedad
                    'url' => HorarioResource::getUrl(
                        name: 'edit',
                        parameters: ['record' => $horario]
                    ),
                ];
            })
            ->toArray();
    }

    // Método para crear el formulario de creación de eventos
    public function getFormSchema(): array
    {
        return [
            TextInput::make('title') // Campo para el título del evento
                ->required() // Asegúrate de que sea obligatorio
                ->label('Título del Evento'),

            Grid::make()
                ->schema([
                    DateTimePicker::make('start_at') // Campo para la fecha de inicio
                        ->required() // Asegúrate de que sea obligatorio
                        ->label('Fecha de Inicio')
                        ->displayFormat('Y-m-d H:i'), // Configura el formato de la fecha

                    DateTimePicker::make('end_at') // Campo para la fecha de finalización
                        ->required() // Asegúrate de que sea obligatorio
                        ->label('Fecha de Fin')
                        ->displayFormat('Y-m-d H:i'), // Configura el formato de la fecha
                ]),
        ];
    }
}
