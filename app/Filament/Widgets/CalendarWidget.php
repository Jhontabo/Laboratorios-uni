<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Schedule::class;

    public static function canView(): bool
    {
        return !request()->routeIs('filament.admin.pages.dashboard');
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'slotMinTime' => '07:00:00',
            'slotMaxTime' => '16:00:00',
            'locale' => 'es',
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'height' => 600,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Schedule::whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(function (Schedule $schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->start_at,
                    'end' => $schedule->end_at,
                    'color' => $schedule->color,
                ];
            })
            ->toArray();
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $record->loadMissing('structured');

                    $form->fill([
                        'title' => $record->title,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'color' => $record->color,
                        'laboratory_id' => $record->laboratory_id,
                        'user_id' => $record->user_id,
                        'academic_program_name' => $record->structured->academic_program_name ?? '',
                        'semester' => $record->structured->semester ?? null,
                        'student_count' => $record->structured->student_count ?? null,
                        'group_count' => $record->structured->group_count ?? null,
                    ]);
                })
                ->action(function (Schedule $record, array $data) {
                    // Actualiza el schedule principal
                    $record->update([
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? $record->color,
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                    ]);

                    // Actualiza o crea los detalles estructurados
                    $record->structured()->updateOrCreate(
                        ['schedule_id' => $record->id],
                        [
                            'academic_program_name' => $data['academic_program_name'],
                            'semester' => $data['semester'],
                            'student_count' => $data['student_count'],
                            'group_count' => $data['group_count'],
                        ]
                    );
                }),

            DeleteAction::make()
                ->before(function (Schedule $record) {
                    if ($record->structured) {
                        $record->structured()->delete();
                    }
                }),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Horario')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at' => $arguments['end'] ?? null,
                        'type' => 'structured',
                    ]);
                })
                ->using(function (array $data, string $model): Model {
                    $schedule = $model::create([
                        'type' => 'structured',
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? '#3b82f6',
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                    ]);

                    $schedule->structured()->create([
                        'academic_program_name' => $data['academic_program_name'],
                        'semester' => $data['semester'],
                        'student_count' => $data['student_count'],
                        'group_count' => $data['group_count'],
                    ]);

                    return $schedule->load('structured');
                })
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Información Básica')
                ->schema([
                    Select::make('laboratory_id')
                        ->label('Laboratorio')
                        ->options(Laboratory::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live(),

                    Select::make('user_id')
                        ->label('Profesor Responsable')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn(Builder $query) => $query->role('docente')
                        )
                        ->getOptionLabelFromRecordUsing(fn(User $user) => "{$user->name} {$user->last_name}")
                        ->searchable(['name', 'last_name'])
                        ->preload()
                        ->required(),

                    TextInput::make('title')
                        ->label('Nombre de la actividad')
                        ->required()
                        ->maxLength(257),
                ])
                ->columns(3),

            Section::make('Detalles Académicos')
                ->schema([
                    Select::make('academic_program_name')
                        ->label('Programa Académico')
                        ->options([
                            'Ingeniería de Sistemas' => 'Ingeniería de Sistemas',
                            'Ingeniería Industrial' => 'Ingeniería Industrial',
                            'Contaduría Pública' => 'Contaduría Pública',
                            'Administración de Empresas' => 'Administración de Empresas',
                        ])
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('semester')
                        ->label('Semestre')
                        ->options(collect(range(2, 10))->mapWithKeys(fn($item) => [$item => (string)$item]))
                        ->required()
                        ->native(false),

                    TextInput::make('student_count')
                        ->label('Número de Estudiantes')
                        ->numeric()
                        ->minValue(4)
                        ->maxValue(103)
                        ->required(),

                    TextInput::make('group_count')
                        ->label('Número de Grupos')
                        ->numeric()
                        ->minValue(4)
                        ->maxValue(23)
                        ->required(),
                ])
                ->columns(4),

            Section::make('Configuración del Horario')
                ->schema([
                    DateTimePicker::make('start_at')
                        ->label('Hora de Inicio')
                        ->required()
                        ->seconds(false),

                    DateTimePicker::make('end_at')
                        ->label('Hora de Finalización')
                        ->required()
                        ->seconds(false)
                        ->after('start_at'),

                    ColorPicker::make('color')
                        ->label('Color del Evento')
                        ->default('#3b82f6'),
                ])
                ->columns(3),
        ];
    }
}
