<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\Product;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\{
    CreateAction,
    EditAction,
    DeleteAction
};

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Schedule::class;

    public static function canView(): bool
    {
        // ADMIN y COORDINADOR ven y usan el widget por igual
        return Auth::check() && Auth::user()->hasAnyRole(['ADMIN', 'COORDINADOR']);
    }

    public function config(): array
    {
        return [
            'firstDay'      => 0,
            'slotMinTime'   => '07:00:00',
            'slotMaxTime'   => '16:00:00',
            'locale'        => 'es',
            'initialView'   => 'timeGridWeek',
            'selectable'    => true,
            'editable'      => true,            // <-- permitir drag & drop
            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'height'   => 600,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $start = $fetchInfo['start'];
        $end   = $fetchInfo['end'];

        return Schedule::query()
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                    ->orWhere(
                        fn($q2) => $q2
                            ->whereNotNull('recurrence_until')
                            ->where('recurrence_until', '>=', $start)
                            ->where('start_at', '<=', $end)
                    );
            })
            ->get()
            ->flatMap(
                fn(Schedule $s) => $s->recurrence_days
                    ? $this->generateRecurringEvents($s, $start, $end)
                    : [$this->formatEvent($s)]
            )
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
                'type'    => $schedule->type,
                'blocked' => $schedule->type === 'structured',
            ],
        ];
    }

    protected function generateRecurringEvents(Schedule $schedule, string $start, string $end): array
    {
        $events    = [];
        $startDate = Carbon::parse($schedule->start_at);
        $endDate   = Carbon::parse($schedule->end_at);
        $length    = $startDate->diffInMinutes($endDate);
        $until     = Carbon::parse($schedule->recurrence_until);
        $days      = explode(',', $schedule->recurrence_days);

        foreach (CarbonPeriod::create($startDate, $until) as $date) {
            if (! in_array($date->dayOfWeek, $days)) continue;
            $s = $date->setTime($startDate->hour, $startDate->minute);
            $e = (clone $s)->addMinutes($length);
            if ($e <= $start || $s >= $end) continue;

            $events[] = [
                'id'           => "{$schedule->id}-{$s->toDateString()}",
                'title'        => $schedule->title,
                'start'        => $s,
                'end'          => $e,
                'color'        => $schedule->color,
                'extendedProps' => [
                    'isRecurring' => true,
                ],
            ];
        }

        return $events;
    }

    protected function processRecurrenceData(array $data): array
    {
        $recurring = $data['is_recurring'] ?? false;
        return [
            'recurrence_days'  => $recurring
                ? implode(',', $data['recurrence_days'] ?? [])
                : null,
            'recurrence_until' => $recurring
                ? $data['recurrence_until']
                : null,
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Práctica')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'is_structured'        => true,
                        'is_recurring'         => false,
                        'recurrence_days'      => [],
                        'recurrence_until'     => null,
                        'start_at'             => $arguments['start'] ?? null,
                        'end_at'               => $arguments['end']   ?? null,
                        'laboratory_id'        => null,
                        'color'                => '#3b82f6',
                        'title'                => null,
                        'academic_program_name' => null,
                        'semester'             => null,
                        'student_count'        => null,
                        'group_count'          => null,
                        'project_type'         => null,
                        'academic_program'     => null,
                        'applicants'           => null,
                        'research_name'        => null,
                        'advisor'              => null,
                    ]);
                })
                ->form($this->getFormSchema())
                ->using(function (array $data): ?Schedule {
                    $start = Carbon::parse($data['start_at']);
                    $end   = Carbon::parse($data['end_at']);

                    if (! $data['start_at'] || ! $data['end_at']) {
                        Notification::make()
                            ->title('Datos incompletos')
                            ->body('Debes indicar inicio y fin.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    if ($end->lte($start) || $end->hour >= 16) {
                        Notification::make()
                            ->title('Horario inválido')
                            ->body('Revisa rango y límite de hora.')
                            ->danger()
                            ->send();
                        return null;
                    }

                    $recurrence = $this->processRecurrenceData($data);
                    $schedule = Schedule::create([
                        'type'             => $data['is_structured'] ? 'structured' : 'unstructured',
                        'title'            => $data['is_structured'] ? $data['title'] : 'Reserva',
                        'start_at'         => $data['start_at'],
                        'end_at'           => $data['end_at'],
                        'color'            => $data['color'],
                        'laboratory_id'    => $data['laboratory_id'] ?? null,
                        'user_id'          => Auth::id(),
                        'recurrence_days'  => $recurrence['recurrence_days'],
                        'recurrence_until' => $recurrence['recurrence_until'],
                    ]);

                    if ($data['is_structured']) {
                        $schedule->structured()->create([
                            'academic_program_name' => $data['academic_program_name'],
                            'semester'             => $data['semester'],
                            'student_count'        => $data['student_count'],
                            'group_count'          => $data['group_count'],
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
                ->visible(fn(?Schedule $r) => $r instanceof Schedule)
                ->mountUsing(function (Schedule $record, Form $form, array $args) {
                    $form->fill([
                        'is_structured'        => $record->type === 'structured',
                        'title'                => $record->title,
                        'laboratory_id'        => $record->laboratory_id,
                        'start_at'             => $args['event']['start'] ?? $record->start_at,
                        'end_at'               => $args['event']['end']   ?? $record->end_at,
                        'color'                => $record->color,
                        'is_recurring'         => $record->recurrence_days !== null,
                        'recurrence_days'      => $record->recurrence_days
                            ? explode(',', $record->recurrence_days)
                            : [],
                        'recurrence_until'     => $record->recurrence_until,
                        'project_type'         => $record->type === 'unstructured'
                            ? $record->unstructured->project_type
                            : null,
                        'academic_program'     => $record->type === 'unstructured'
                            ? $record->unstructured->academic_program
                            : null,
                        'semester'             => $record->type === 'unstructured'
                            ? $record->unstructured->semester
                            : null,
                        'applicants'           => $record->type === 'unstructured'
                            ? $record->unstructured->applicants
                            : null,
                        'research_name'        => $record->type === 'unstructured'
                            ? $record->unstructured->research_name
                            : null,
                        'advisor'              => $record->type === 'unstructured'
                            ? $record->unstructured->advisor
                            : null,
                        'academic_program_name' => $record->type === 'structured'
                            ? $record->structured->academic_program_name
                            : null,
                        'student_count'        => $record->type === 'structured'
                            ? $record->structured->student_count
                            : null,
                        'group_count'          => $record->type === 'structured'
                            ? $record->structured->group_count
                            : null,
                    ]);
                })
                ->form($this->getFormSchema())
                ->action(function (Schedule $record, array $data) {
                    $start = Carbon::parse($data['start_at']);
                    $end   = Carbon::parse($data['end_at']);

                    if ($end->lte($start) || $end->hour >= 16) {
                        Notification::make()
                            ->title('Horario inválido')
                            ->body('Revisa hora de fin.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $recurrence = $this->processRecurrenceData($data);
                    $record->update([
                        'type'             => $data['is_structured'] ? 'structured' : 'unstructured',
                        'title'            => $data['is_structured'] ? $data['title'] : $record->title,
                        'laboratory_id'    => $data['laboratory_id'],
                        'start_at'         => $data['start_at'],
                        'end_at'           => $data['end_at'],
                        'color'            => $data['color'],
                        'recurrence_days'  => $recurrence['recurrence_days'],
                        'recurrence_until' => $recurrence['recurrence_until'],
                    ]);
                }),

            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn(?Schedule $r) => $r instanceof Schedule)
                ->before(fn(Schedule $r) => tap($r->{$r->type}(), fn($q) => $q->delete())->getModel()->delete()),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Toggle::make('is_structured')
                ->label('¿Práctica estructurada?')
                ->reactive()
                ->default(true),

            // Sección Estructurada
            Section::make('PRÁCTICA ESTRUCTURADA')
                ->visible(fn($get) => $get('is_structured'))
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
                    TextInput::make('title')
                        ->label('Nombre de la práctica')
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
                    Section::make('Recurrencia')
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
                                ->minDate(
                                    fn($get) => $get('start_at')
                                        ? Carbon::parse($get('start_at'))->addDay()
                                        : null
                                )
                                ->visible(fn($get) => $get('is_recurring')),
                        ]),
                    Section::make('Horario estructurada')
                        ->columns(3)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->label('Inicio')
                                ->required()
                                ->seconds(false),
                            DateTimePicker::make('end_at')
                                ->label('Fin')
                                ->required()
                                ->seconds(false)
                                ->after('start_at'),
                            ColorPicker::make('color')
                                ->label('Color')
                                ->default('#3b82f6'),
                        ]),
                ]),

            // Sección No Estructurada
            Section::make('PRÁCTICA NO ESTRUCTURADA')
                ->visible(fn($get) => ! $get('is_structured'))
                ->columns(4)
                ->schema([
                    Section::make('Horario no estructurada')
                        ->columns(3)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->label('Inicio')
                                ->required()
                                ->seconds(false),
                            DateTimePicker::make('end_at')
                                ->label('Fin')
                                ->required()
                                ->seconds(false)
                                ->after('start_at'),
                            ColorPicker::make('color')
                                ->label('Color')
                        ]),
                ]),
            Section::make('Recurrencia')
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
                        ->minDate(
                            fn($get) => $get('start_at')
                                ? Carbon::parse($get('start_at'))->addDay()
                                : null
                        )
                        ->visible(fn($get) => $get('is_recurring')),
                ]),

            // Horario común

        ];
    }
}
