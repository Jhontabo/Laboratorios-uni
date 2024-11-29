<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Filament\Resources\HorarioResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\CreateAction;

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
                'left' => 'dayGridMonth,dayGridWeek,dayGridDay',
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
                    'color' => $horario->color,
                ];
            })
            ->toArray();
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Horario $record, Form $form, array $arguments) {
                        // Asegúrate de que 'arguments' contiene los datos esperados
                        $form->fill([
                            'title' => $record->title, // Usar el título del evento
                            'start_at' => $arguments['event']['start'] ?? $record->start_at, // Usa el evento 'start' si existe
                            'end_at' => $arguments['event']['end'] ?? $record->end_at, // Usa el evento 'end' si existe
                            'color' => $record->color, // Agregar el color del evento
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }


    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $form->fill([
                            'start_at' => $arguments['start'] ?? null,
                            'end_at' => $arguments['end'] ?? null
                        ]);
                    }
                )
        ];
    }


    // Método para crear el formulario de creación de eventos
    public function getFormSchema(): array
    {
        return [
            TextInput::make('title') // Campo para el título del evento
                ->required() // Asegúrate de que sea obligatorio
                ->label('Título del Evento'),
            ColorPicker::make(name: 'color'),

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
