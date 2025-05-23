<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\{
    CheckboxList,
    ColorPicker,
    DatePicker,
    DateTimePicker,
    Radio,
    Section,
    Select,
    TextInput,
    Toggle
};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\{
    CreateAction,
    EditAction,
    DeleteAction
};
use Illuminate\Support\Js;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Schedule::class;

    public static function canView(): bool
    {
        return ! request()->routeIs('filament.admin.pages.dashboard') && Auth::check();
    }



    public function config(): array
    {
        return [
            'firstDay'      => 0,
            'slotMinTime'   => '07:00:00',
            'slotMaxTime'   => '16:00:00',
            'locale'        => 'es',
            'initialView'   => 'timeGridWeek',

            // Habilita selección de rangos
            'selectable'    => true,

            // Sólo permite seleccionar donde NO haya un evento structured
            'selectAllow'   => "function(selectInfo) {
            return ! this.getEvents().some(function(ev) {
                return ev.extendedProps.type === 'structured'
                    && ev.start < selectInfo.end
                    && ev.end   > selectInfo.start;
            });
        }",

            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'height'   => 600,
            'editable' => false,
        ];
    }


    public function fetchEvents(array $fetchInfo): array
    {
        $start = $fetchInfo['start'];
        $end   = $fetchInfo['end'];

        $query = Schedule::query();

        if (Auth::user()->hasRole('COORDINADOR')) {
            $query->where('type', 'structured');
        }

        $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_at', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->whereNotNull('recurrence_until')
                        ->where('recurrence_until', '>=', $start)
                        ->where('start_at', '<=', $end);
                });
        });

        return $query->get()
            ->flatMap(function (Schedule $schedule) use ($start, $end) {
                if (! $schedule->recurrence_days) {
                    return [$this->formatEvent($schedule)];
                }
                return $this->generateRecurringEvents($schedule, $start, $end);
            })
            ->toArray();
    }

    protected function formatEvent(Schedule $schedule): array
    {
        return [
            'id'           => $schedule->id,
            'title'        => $schedule->title,
            'start'        => $schedule->start_at,
            'end'          => $schedule->end_at,
            'color'        => $schedule->color,
            'extendedProps' => [
                'type' => $schedule->type,
            ],
        ];
    }

    protected function generateRecurringEvents(Schedule $schedule, string $start, string $end): array
    {
        $events          = [];
        $startDate       = Carbon::parse($schedule->start_at);
        $endDate         = Carbon::parse($schedule->end_at);
        $durationMinutes = $startDate->diffInMinutes($endDate);
        $recurrenceUntil = Carbon::parse($schedule->recurrence_until);
        $daysOfWeek      = explode(',', $schedule->recurrence_days);

        foreach (CarbonPeriod::create($startDate, $recurrenceUntil) as $date) {
            if (! in_array($date->dayOfWeek, $daysOfWeek)) {
                continue;
            }
            $eventStart = $date->setTime($startDate->hour, $startDate->minute);
            $eventEnd   = (clone $eventStart)->addMinutes($durationMinutes);
            if ($eventEnd < $start || $eventStart > $end) {
                continue;
            }
            $events[] = [
                'id'            => $schedule->id . '-' . $eventStart->toDateString(),
                'title'         => $schedule->title,
                'start'         => $eventStart,
                'end'           => $eventEnd,
                'color'         => $schedule->color,
                'extendedProps' => [
                    'isRecurring' => true,
                    'parentId'    => $schedule->id,
                ],
            ];
        }

        return $events;
    }

    protected function processRecurrenceData(array $data): array
    {
        $result = ['recurrence_days' => null, 'recurrence_until' => null];
        if ($data['is_recurring'] ?? false) {
            $result['recurrence_days']  = implode(',', $data['recurrence_days'] ?? []);
            $result['recurrence_until'] = $data['recurrence_until'];
        }
        return $result;
    }

    protected function hasStructuredOverlap(int $labId, Carbon $start, Carbon $end): bool
    {
        return Schedule::where('type', 'structured')
            ->where('laboratory_id', $labId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at',   [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_at', '<=', $start)
                            ->where('end_at',   '>=', $end);
                    });
            })
            ->exists();
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label(fn() => Auth::user()->hasRole('COORDINADOR')
                    ? 'Crear Bloque Estructurado'
                    : 'Crear Práctica No Estructurada')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at'         => $arguments['start'] ?? null,
                        'end_at'           => $arguments['end']   ?? null,
                        'laboratory_id'    => null,
                        'color'            => '#3b82f6',
                        'is_recurring'     => false,
                        'recurrence_days'  => [],
                        'recurrence_until' => null,
                        'title'            => null,
                        'project_type'     => null,
                        'academic_program' => null,
                        'semester'         => null,
                        'applicants'       => null,
                        'research_name'    => null,
                        'advisor'          => null,
                    ]);
                })
                ->form($this->getFormSchema())
                ->using(function (array $data): Schedule {
                    $start = Carbon::parse($data['start_at']);
                    $end   = Carbon::parse($data['end_at']);
                    $labId = $data['laboratory_id'];

                    if (
                        ! Auth::user()->hasRole('COORDINADOR')
                        && $this->hasStructuredOverlap($labId, $start, $end)
                    ) {
                        throw ValidationException::withMessages([
                            'laboratory_id' => 'Ese espacio ya está bloqueado por un bloque estructurado.',
                        ]);
                    }

                    if ($end->hour >= 16) {
                        throw ValidationException::withMessages([
                            'end_at' => 'La hora de finalización no puede ser después de las 16:00.',
                        ]);
                    }

                    $type       = Auth::user()->hasRole('COORDINADOR') ? 'structured' : 'unstructured';
                    $recurrence = $this->processRecurrenceData($data);

                    $schedule = Schedule::create([
                        'type'            => $type,
                        'title'           => $type === 'structured' ? $data['title'] : 'Reserva',
                        'start_at'        => $data['start_at'],
                        'end_at'          => $data['end_at'],
                        'color'           => $data['color'],
                        'laboratory_id'   => $labId,
                        'user_id'         => Auth::id(),
                        'recurrence_days'  => $recurrence['recurrence_days'],
                        'recurrence_until' => $recurrence['recurrence_until'],
                    ]);

                    if ($type === 'structured') {
                        $schedule->structured()->create([
                            'academic_program_name' => $data['academic_program_name'],
                            'semester'              => $data['semester'],
                            'student_count'         => $data['student_count'],
                            'group_count'           => $data['group_count'],
                        ]);
                    } else {
                        $schedule->unstructured()->create([
                            'project_type'     => $data['project_type'],
                            'academic_program' => $data['academic_program'],
                            'semester'         => $data['semester'],
                            'applicants'       => $data['applicants'],
                            'research_name'    => $data['research_name'],
                            'advisor'          => $data['advisor'],
                        ]);
                    }

                    return $schedule;
                }),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn(?Schedule $r) => $r instanceof Schedule && (
                    (Auth::user()->hasRole('COORDINADOR') && $r->type === 'structured') ||
                    (! Auth::user()->hasRole('COORDINADOR') && $r->type === 'unstructured')
                ))
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $form->fill([
                        'title'                  => $record->title,
                        'laboratory_id'          => $record->laboratory_id,
                        'start_at'               => $arguments['event']['start'] ?? $record->start_at,
                        'end_at'                 => $arguments['event']['end']   ?? $record->end_at,
                        'color'                  => $record->color,
                        'academic_program_name'  => optional($record->structured)->academic_program_name,
                        'semester'               => optional($record->structured)->semester,
                        'student_count'          => optional($record->structured)->student_count,
                        'group_count'            => optional($record->structured)->group_count,
                        'is_recurring'           => $record->recurrence_days !== null,
                        'recurrence_days'        => $record->recurrence_days ? explode(',', $record->recurrence_days) : [],
                        'recurrence_until'       => $record->recurrence_until,
                        'project_type'           => optional($record->unstructured)->project_type,
                        'academic_program'       => optional($record->unstructured)->academic_program,
                        'applicants'             => optional($record->unstructured)->applicants,
                        'research_name'          => optional($record->unstructured)->research_name,
                        'advisor'                => optional($record->unstructured)->advisor,
                    ]);
                })
                ->form($this->getFormSchema())
                ->action(function (Schedule $record, array $data) {
                    $start = Carbon::parse($data['start_at']);
                    $end   = Carbon::parse($data['end_at']);
                    $labId = $data['laboratory_id'];

                    if (
                        ! Auth::user()->hasRole('COORDINADOR')
                        && $this->hasStructuredOverlap($labId, $start, $end)
                    ) {
                        throw ValidationException::withMessages([
                            'start_at' => 'No puedes mover esta práctica; el espacio está bloqueado por un bloque estructurado.',
                        ]);
                    }

                    if ($end->hour >= 16) {
                        throw ValidationException::withMessages([
                            'end_at' => 'La hora de finalización no puede ser después de las 16:00.',
                        ]);
                    }

                    $recurrence = $this->processRecurrenceData($data);
                    $record->update([
                        'title'            => $data['title'] ?? $record->title,
                        'laboratory_id'    => $labId,
                        'start_at'         => $data['start_at'],
                        'end_at'           => $data['end_at'],
                        'color'            => $data['color'],
                        'recurrence_days'  => $recurrence['recurrence_days'],
                        'recurrence_until' => $recurrence['recurrence_until'],
                    ]);

                    if ($record->type === 'structured') {
                        $record->structured()->updateOrCreate(
                            ['schedule_id' => $record->id],
                            [
                                'academic_program_name' => $data['academic_program_name'],
                                'semester'              => $data['semester'],
                                'student_count'         => $data['student_count'],
                                'group_count'           => $data['group_count'],
                            ]
                        );
                    } else {
                        $record->unstructured()->update([
                            'project_type'     => $data['project_type'],
                            'academic_program' => $data['academic_program'],
                            'semester'         => $data['semester'],
                            'applicants'       => $data['applicants'],
                            'research_name'    => $data['research_name'],
                            'advisor'          => $data['advisor'],
                        ]);
                    }
                }),

            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn(?Schedule $r) => $r instanceof Schedule && (
                    (Auth::user()->hasRole('COORDINADOR') && $r->type === 'structured') ||
                    (! Auth::user()->hasRole('COORDINADOR') && $r->type === 'unstructured')
                ))
                ->before(fn(Schedule $r) => tap($r->{$r->type}(), fn($q) => $q->delete())->getModel()->delete()),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('PRÁCTICA ESTRUCTURADA')
                ->visible(fn() => Auth::user()->hasRole('COORDINADOR'))
                ->columns(4)
                ->schema([
                    Select::make('academic_program_name')
                        ->label('Programa académico')
                        ->options([
                            'Ingeniería de Sistemas'     => 'Ingeniería de Sistemas',
                            'Ingeniería Industrial'      => 'Ingeniería Industrial',
                            'Contaduría Pública'         => 'Contaduría Pública',
                            'Administración de Empresas' => 'Administración de Empresas',
                        ])
                        ->required()
                        ->columnSpan(4),
                    Select::make('laboratory_id')
                        ->label('Espacio académico')
                        ->options(Laboratory::pluck('name', 'id'))
                        ->required()
                        ->columnSpan(4),
                    Select::make('semester')
                        ->label('Semestre')
                        ->options(array_combine(range(1, 10), range(1, 10)))
                        ->required()
                        ->columnSpan(4),
                    Select::make('user_id')
                        ->label('Profesor responsable')
                        ->options(User::role('COORDINADOR')->pluck('name', 'id'))
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('title')
                        ->label('Nombre de la práctica académica')
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('student_count')
                        ->label('Número de estudiantes')
                        ->numeric()
                        ->required()
                        ->columnSpan(2),
                    TextInput::make('group_count')
                        ->label('Número de grupos')
                        ->numeric()
                        ->required()
                        ->columnSpan(2),
                ]),

            Section::make('Recurrencia')
                ->visible(fn() => Auth::user()->hasRole('COORDINADOR'))
                ->columns(1)
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
                        ->visible(fn($get) => $get('is_recurring')),
                    DatePicker::make('recurrence_until')
                        ->label('Repetir hasta')
                        ->minDate(fn($get) => $get('start_at') ? Carbon::parse($get('start_at'))->addDay() : null)
                        ->visible(fn($get) => $get('is_recurring')),
                ]),

            Section::make('PRÁCTICA NO ESTRUCTURADA')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(4)
                ->schema([
                    Radio::make('project_type')
                        ->label('Proyecto integrador')
                        ->options([
                            'Trabajo de grado'         => 'Trabajo de grado',
                            'Investigación profesoral' => 'Investigación profesoral',
                        ])
                        ->columns(3)
                        ->columnSpan(4)
                        ->required(),
                    Select::make('laboratory_id')
                        ->label('Espacio académico')
                        ->options(Laboratory::pluck('name', 'id'))
                        ->required()
                        ->columnSpan(4),
                    Select::make('academic_program')
                        ->label('Programa académico')
                        ->options([
                            'Ingeniería de Sistemas'     => 'Ingeniería de Sistemas',
                            'Ingeniería Industrial'      => 'Ingeniería Industrial',
                            'Contaduría Pública'         => 'Contaduría Pública',
                            'Administración de Empresas' => 'Administración de Empresas',
                        ])
                        ->required()
                        ->columnSpan(4),
                    Select::make('semester')
                        ->label('Semestre')
                        ->options(array_combine(range(1, 10), range(1, 10)))
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('applicants')
                        ->label('Nombre de los solicitantes')
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('research_name')
                        ->label('Nombre de la investigación')
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('advisor')
                        ->label('Nombre del asesor')
                        ->required()
                        ->columnSpan(4),
                ]),

            Section::make('MATERIALES Y EQUIPOS')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(1)
                ->schema([
                    Select::make('products')
                        ->label('Productos disponibles')
                        ->multiple()
                        ->reactive()
                        ->options(function () {
                            return Product::with('laboratory')
                                ->get()
                                ->mapWithKeys(fn($product) => [
                                    $product->id => "{$product->name} — {$product->laboratory->name}",
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->required(),
                ]),

            Section::make('Horario')
                ->columns(3)
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
                ]),
        ];
    }
}
