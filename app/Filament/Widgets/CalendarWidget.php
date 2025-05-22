<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
            'firstDay' => 0,
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
        $start = $fetchInfo['start'];
        $end = $fetchInfo['end'];

        return Schedule::where(function ($query) use ($start, $end) {
            $query->whereBetween('start_at', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->whereNotNull('recurrence_until')
                        ->where('recurrence_until', '>=', $start)
                        ->where('start_at', '<=', $end);
                });
        })
            ->get()
            ->flatMap(function (Schedule $schedule) use ($start, $end) {
                if (!$schedule->recurrence_days) {
                    return [$this->formatEvent($schedule)];
                }

                return $this->generateRecurringEvents($schedule, $start, $end);
            })
            ->toArray();
    }

    protected function formatEvent(Schedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => $schedule->start_at,
            'end' => $schedule->end_at,
            'color' => $schedule->color,
        ];
    }

    protected function generateRecurringEvents(Schedule $schedule, string $start, string $end): array
    {
        $events = [];
        $startDate = Carbon::parse($schedule->start_at);
        $endDate = Carbon::parse($schedule->end_at);
        $duration = $startDate->diffInMinutes($endDate);
        $recurrenceUntil = Carbon::parse($schedule->recurrence_until);
        $daysOfWeek = explode(',', $schedule->recurrence_days);

        $period = CarbonPeriod::create($startDate, $recurrenceUntil);

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $daysOfWeek)) {
                continue;
            }

            $eventStart = $date->setTime($startDate->hour, $startDate->minute);
            $eventEnd = (clone $eventStart)->addMinutes($duration);

            // Solo incluir eventos dentro del rango solicitado
            if ($eventEnd < $start || $eventStart > $end) {
                continue;
            }

            $events[] = [
                'id' => $schedule->id . '-' . $eventStart->format('Y-m-d'),
                'title' => $schedule->title,
                'start' => $eventStart,
                'end' => $eventEnd,
                'color' => $schedule->color,
                'extendedProps' => [
                    'isRecurring' => true,
                    'parentId' => $schedule->id,
                ],
            ];
        }

        return $events;
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
                        'is_recurring' => $record->recurrence_days !== null,
                        'recurrence_days' => $record->recurrence_days ? explode(',', $record->recurrence_days) : [],
                        'recurrence_until' => $record->recurrence_until,
                        'academic_program_name' => $record->structured->academic_program_name ?? '',
                        'semester' => $record->structured->semester ?? null,
                        'student_count' => $record->structured->student_count ?? null,
                        'group_count' => $record->structured->group_count ?? null,
                    ]);
                })
                ->action(function (Schedule $record, array $data) {
                    $recurrenceData = $this->processRecurrenceData($data);

                    $record->update([
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? $record->color,
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                        'recurrence_days' => $recurrenceData['recurrence_days'],
                        'recurrence_until' => $recurrenceData['recurrence_until'],
                    ]);

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

                ->using(function (array $data, string $model): ?Model {
                    $startDay = Carbon::parse($data['start_at'])->dayOfWeek;

                    if (in_array($startDay, [0, 6])) {
                        Notification::make()
                            ->title('Día no permitido')
                            ->body('No se pueden crear horarios los días sábado o domingo.')
                            ->danger()
                            ->send();

                        return null; // <- esto evita que se cree el horario
                    }

                    $recurrenceData = $this->processRecurrenceData($data);

                    $schedule = $model::create([
                        'type' => 'structured',
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? '#3b82f6',
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                        'recurrence_days' => $recurrenceData['recurrence_days'],
                        'recurrence_until' => $recurrenceData['recurrence_until'],
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

    protected function processRecurrenceData(array $data): array
    {
        $result = [
            'recurrence_days' => null,
            'recurrence_until' => null,
        ];

        if ($data['is_recurring'] ?? false) {
            $result['recurrence_days'] = implode(',', $data['recurrence_days']);
            $result['recurrence_until'] = $data['recurrence_until'];
        }

        return $result;
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
                        ->options(collect(range(1, 10))->mapWithKeys(fn($item) => [$item => (string)$item]))
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
                        ->label('Fecha y Hora de Inicio')
                        ->required()
                        ->seconds(false),

                    DateTimePicker::make('end_at')
                        ->label('Fecha y Hora de Finalización')
                        ->required()
                        ->seconds(false)
                        ->after('start_at'),

                    ColorPicker::make('color')
                        ->label('Color del Evento')
                        ->default('#3b82f6'),
                ])
                ->columns(3),

            Section::make('Recurrencia')
                ->schema([
                    Toggle::make('is_recurring')
                        ->label('Evento recurrente')
                        ->reactive(),

                    CheckboxList::make('recurrence_days')
                        ->label('Días de la semana')
                        ->options([
                            '1' => 'Lunes',
                            '2' => 'Martes',
                            '3' => 'Miércoles',
                            '4' => 'Jueves',
                            '5' => 'Viernes',
                        ])
                        ->columns(5)
                        ->visible(fn(callable $get) => $get('is_recurring')),

                    DatePicker::make('recurrence_until')
                        ->label('Repetir hasta')
                        ->minDate(fn(callable $get) => $get('start_at') ? Carbon::parse($get('start_at'))->addDay() : null)
                        ->visible(fn(callable $get) => $get('is_recurring')),
                ])
                ->columns(1),
        ];
    }
}
