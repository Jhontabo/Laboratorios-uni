<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Filament\Resources\HorarioResource;
use App\Models\Laboratorio;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario Horarios';

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
            'firstDay' => 1, // Inicia la semana en lunes
            'slotMinTime' => '06:00:00', // Hora mínima visible
            'slotMaxTime' => '22:00:00', // Hora máxima visible
            'slotDuration' => '00:30:00', // Intervalo de tiempo de cada bloque
            'locale' => 'es',
            'initialView' => 'timeGridWeek', // Vista semanal predeterminada
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay', // Opciones de vista
            ],

        ];
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

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Horario $record, Form $form, array $arguments) {
                        // Llena el formulario con los valores actuales del registro
                        $form->fill([
                            'title' => $record->title, // Título del evento
                            'start_at' => $arguments['event']['start'] ?? $record->start_at, // Usa la fecha inicial del evento o del registro
                            'end_at' => $arguments['event']['end'] ?? $record->end_at, // Usa la fecha de fin del evento o del registro
                            'color' => $record->color, // Color del evento
                            'is_available' => $record->is_available, // Disponibilidad
                            'id_laboratorio' => $record->id_laboratorio, // Relación con el laboratorio
                        ]);
                    }
                )
                ->action(
                    function (Horario $record, array $data) {
                        // Actualiza los datos del evento en la base de datos
                        $record->update([
                            'title' => $data['title'], // Actualiza el título
                            'start_at' => $data['start_at'], // Actualiza la fecha de inicio
                            'end_at' => $data['end_at'], // Actualiza la fecha de fin
                            'color' => $data['color'], // Actualiza el color
                            'is_available' => $data['is_available'], // Actualiza la disponibilidad
                            'id_laboratorio' => $data['id_laboratorio'], // Actualiza el laboratorio
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

    public function getFormSchema(): array
    {
        return [
            Section::make('Información General')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->label('Nombre')
                        ->placeholder('Ingrese el nombre del evento'),

                    TextArea::make('description')
                        ->label('Descripción')
                        ->maxLength(500)
                        ->placeholder('Ejemplo: Clase de programación avanzada')
                        ->helperText('La descripción no debe exceder los 500 caracteres'),
                ])
                ->columns(2), // Dividido en dos columnas

            Section::make('Disponibilidad y color')
                ->schema([
                    Toggle::make('is_available')
                        ->label('Disponibilidad para reserva')
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Elige si el espacio estará disponible para reserva.')
                        ->default(false),
                    ColorPicker::make('color')
                        ->label('Color del evento')
                        ->helperText('Elige un color para representar este evento.'),

                    Section::make('Laboratorio')
                        ->schema([
                            Select::make('id_laboratorio')
                                ->label('Laboratorio')
                                ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
                                ->required(),
                        ]),
                ])->columns(2),

            Section::make('Horario')
                ->schema([
                    Grid::make(2) // Dividido en dos columnas
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->required()
                                ->label('Fecha y hora de inicio')
                                ->placeholder('Seleccione la fecha y hora de inicio')
                                ->displayFormat('Y-m-d H:i')
                                ->helperText('No se puede seleccionar una fecha pasada')
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state && $state < now()) {
                                        $set('start_at', null); // Limpia el campo si es inválido
                                        return 'La fecha de inicio no puede ser anterior a la actual.';
                                    }
                                }),

                            DateTimePicker::make('end_at')
                                ->required()
                                ->label('Fecha y hora de fin')
                                ->placeholder('Seleccione la fecha y hora de fin')
                                ->displayFormat('Y-m-d H:i')
                                ->helperText('Debe ser posterior a la fecha de inicio')
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    if ($state && $state < $get('start_at')) {
                                        $set('end_at', null); // Limpia el campo si es inválido
                                        return 'La fecha de fin debe ser posterior a la de inicio.';
                                    }
                                }),
                        ]),
                ])
                ->columns(2), // Diseño en dos columnas
        ];
    }
}
