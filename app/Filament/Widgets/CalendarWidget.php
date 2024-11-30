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
use Filament\Forms\Components\Textarea;

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
                            'end_at' => $arguments['event']['end'] ?? $record->end_at,
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
                ->label('Motivo de la reserva'),

            TextArea::make('description')
                ->label('Descripción del evento')
                ->maxLength(500)
                ->helperText('La descripción no debe exceder los 500 caracteres'),

            Grid::make()
                ->schema([
                    DateTimePicker::make('start_at') // Campo para la fecha de inicio
                        ->required() // Asegúrate de que sea obligatorio
                        ->label('Fecha de Inicio')
                        ->displayFormat('Y-m-d H:i')
                        ->helperText('No se puede seleccionar una fecha pasada')
                        // Validación de fecha de inicio posterior a la fecha actual
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state && $state < now()) {
                                // Añadir un mensaje de error visual
                                $set('start_at', null); // Limpiar el campo si la fecha es pasada
                                return 'La fecha de inicio no puede ser anterior a la fecha actual.';
                            }
                        }),

                    DateTimePicker::make('end_at') // Campo para la fecha de finalización
                        ->required() // Asegúrate de que sea obligatorio
                        ->label('Fecha de Fin')
                        ->displayFormat('Y-m-d H:i')
                        ->helperText('No se puede seleccionar una fecha pasada')
                        // Validación de fecha de fin posterior a la fecha de inicio
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            // Validar que la fecha de fin no sea anterior a la de inicio
                            if ($state && $state < $get('start_at')) {
                                $set('end_at', null);
                                return 'La fecha y hora de fin deben ser posteriores a la de inicio.';
                            }

                            // Validación de que la fecha de fin no sea anterior a la actual
                            if ($state && $state < now()) {
                                return 'La fecha de fin no puede ser anterior a la fecha actual.';
                            }
                        }),
                ]),
        ];
    }
}
