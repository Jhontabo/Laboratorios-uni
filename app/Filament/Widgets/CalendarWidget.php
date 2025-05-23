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
            'selectable'    => true,
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
                ->orWhere(
                    fn($q2) => $q2
                        ->whereNotNull('recurrence_until')
                        ->where('recurrence_until', '>=', $start)
                        ->where('start_at', '<=', $end)
                );
        });

        return $query->get()
            ->flatMap(
                fn(Schedule $schedule) => $schedule->recurrence_days
                    ? $this->generateRecurringEvents($schedule, $start, $end)
                    : [$this->formatEvent($schedule)]
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
            if (! in_array($date->dayOfWeek, $days)) {
                continue;
            }
            $s = $date->setTime($startDate->hour, $startDate->minute);
            $e = (clone $s)->addMinutes($length);

            if ($e <= $start || $s >= $end) {
                continue;
            }

            $events[] = [
                'id'           => "{$schedule->id}-{$s->toDateString()}",
                'title'        => $schedule->title,
                'start'        => $s,
                'end'          => $e,
                'color'        => $schedule->color,
                'extendedProps' => [
                    'isRecurring' => true,
                    'parentId'    => $schedule->id,
                    'blocked'     => $schedule->type === 'structured',
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

    protected function hasStructuredOverlap(int $labId, Carbon $start, Carbon $end): bool
    {
        return Schedule::where('type', 'structured')
            ->where('laboratory_id', $labId)
            ->where('start_at', '<', $end)
            ->where('end_at',   '>', $start)
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
                        'start_at'             => $arguments['start']     ?? null,
                        'end_at'               => $arguments['end']       ?? null,
                        'laboratory_id'        => null,
                        'color'                => '#3b82f6',
                        'is_recurring'         => false,
                        'recurrence_days'      => [],
                        'recurrence_until'     => null,
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
                    $labId = $data['laboratory_id'];

                    // validaciones básicas
                    if (! $data['start_at'] || ! $data['end_at'] || ! $labId) {
                        Notification::make()
                            ->title('Datos incompletos')
                            ->body('Debes indicar inicio, fin y espacio.')
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
                    // solapamiento para no coordinadores
                    if (
                        ! Auth::user()->hasRole('COORDINADOR')
                        && $this->hasStructuredOverlap($labId, $start, $end)
                    ) {
                        Notification::make()
                            ->title('Espacio ocupado')
                            ->body('Ya reservado para clases.')
                            ->danger()
                            ->send();
                        return null;
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
                        'recurrence_days' => $recurrence['recurrence_days'],
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
                ->visible(fn(?Schedule $r) => $r instanceof Schedule)
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $form->fill([
                        'title'           => $record->title,
                        'laboratory_id'   => $record->laboratory_id,
                        'start_at'        => $arguments['event']['start'] ?? $record->start_at,
                        'end_at'          => $arguments['event']['end']   ?? $record->end_at,
                        'color'           => $record->color,
                        'is_recurring'    => $record->recurrence_days !== null,
                        'recurrence_days' => $record->recurrence_days ? explode(',', $record->recurrence_days) : [],
                        'recurrence_until' => $record->recurrence_until,
                        // demás campos según tipo...
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
                        Notification::make()
                            ->title('No permitido')
                            ->body('Espacio reservado para clases.')
                            ->danger()
                            ->send();
                        return;
                    }

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
                        'title'            => $data['title'] ?? $record->title,
                        'laboratory_id'    => $labId,
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
            // Bloque Estructurado (solo COORDINADOR)
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
                ]),

            // Recurrencia (solo COORDINADOR)
            Section::make('Recurrencia')
                ->visible(fn() => Auth::user()->hasRole('COORDINADOR'))
                ->columns(1)
                ->schema([
                    Toggle::make('is_recurring')->label('Evento recurrente')->reactive(),
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

            // Práctica No Estructurada (resto de roles)
            Section::make('PRÁCTICA NO ESTRUCTURADA')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(4)
                ->schema([
                    Radio::make('project_type')
                        ->label('Tipo de proyecto')
                        ->options([
                            'Proyecto integrador'      => 'Proyecto integrador',
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
                        ->label('Solicitantes')
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('research_name')
                        ->label('Investigación')
                        ->required()
                        ->columnSpan(4),
                    TextInput::make('advisor')
                        ->label('Asesor')
                        ->required()
                        ->columnSpan(4),
                ]),

            // Materiales y equipos
            Section::make('MATERIALES Y EQUIPOS')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(1)
                ->schema([
                    Select::make('products')
                        ->label('Productos disponibles')
                        ->multiple()
                        ->reactive()
                        ->options(
                            fn() => Product::with('laboratory')
                                ->get()
                                ->mapWithKeys(fn($p) => [
                                    $p->id => "{$p->name} — {$p->laboratory->name}",
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->required(),
                ]),

            // Horario común
            Section::make('Horario común')
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
        ];
    }
}
