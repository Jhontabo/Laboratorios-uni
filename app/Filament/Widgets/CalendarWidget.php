<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\{
    ColorPicker,
    DateTimePicker,
    Radio,
    Section,
    Select,
    TextInput
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
        if (request()->routeIs('filament.admin.pages.dashboard')) {
            return false;
        }

        return Auth::check();
    }

    public function config(): array
    {
        return [
            'firstDay'      => 0,
            'slotMinTime'   => '07:00:00',
            'slotMaxTime'   => '16:00:00',
            'locale'        => 'es',
            'initialView'   => 'timeGridWeek',
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
        $query = Schedule::whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']]);

        if (Auth::user()->hasRole('COORDINADOR')) {
            $query->where('user_id', Auth::id());
        }

        return $query->get()
            ->map(fn(Schedule $s) => [
                'id'           => $s->id,
                'title'        => $s->title,
                'start'        => $s->start_at,
                'end'          => $s->end_at,
                'color'        => $s->color,
                'extendedProps' => [
                    'type'    => $s->type,
                    'ownerId' => $s->user_id,
                ],
            ])
            ->toArray();
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
                ->form($this->getFormSchema())
                ->using(function (array $data): ?Schedule {
                    $user  = Auth::user();
                    $role  = $user->getRoleNames()->first();
                    $start = Carbon::parse($data['start_at']);
                    $end   = Carbon::parse($data['end_at']);

                    if ($end->hour >= 16) {
                        Notification::make()
                            ->title('Hora inválida')
                            ->body('La hora de finalización no puede ser después de las 16:00.')
                            ->danger()
                            ->send();

                        return null;
                    }

                    $type = $role === 'COORDINADOR' ? 'structured' : 'unstructured';

                    if ($type === 'unstructured') {
                        $conflict = Schedule::where('type', 'structured')
                            ->where(
                                fn($q) => $q
                                    ->whereBetween('start_at', [$start, $end])
                                    ->orWhereBetween('end_at',   [$start, $end])
                                    ->orWhere(
                                        fn($q2) => $q2
                                            ->where('start_at', '<=', $start)
                                            ->where('end_at',   '>=', $end)
                                    )
                            )
                            ->exists();

                        if ($conflict) {
                            Notification::make()
                                ->title('Espacio ocupado')
                                ->body('No puedes solapar una práctica sobre un bloque estructurado.')
                                ->danger()
                                ->send();

                            return null;
                        }
                    }

                    $schedule = Schedule::create([
                        'type'          => $type,
                        'title'         => $type === 'structured' ? $data['title'] : 'Reserva',
                        'start_at'      => $data['start_at'],
                        'end_at'        => $data['end_at'],
                        'color'         => $data['color'] ?? '#3b82f6',
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id'       => $user->id,
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
                            'equipment'        => $data['equipment'] ?? null,
                        ]);
                    }

                    return $schedule;
                }),
        ];
    }

    protected function modalActions(): array
    {
        $role = Auth::user()->getRoleNames()->first();

        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn(?Schedule $record) => $record instanceof Schedule && (
                    ($role === 'COORDINADOR' && $record->type === 'structured') ||
                    (! Auth::user()->hasRole('COORDINADOR') && $record->type === 'unstructured')
                ))
                ->form($this->getFormSchema())
                ->mountUsing(function (Schedule $record, Form $form, array $args) {
                    $base = [
                        'start_at'      => $args['event']['start'] ?? $record->start_at,
                        'end_at'        => $args['event']['end']   ?? $record->end_at,
                        'color'         => $record->color,
                        'laboratory_id' => $record->laboratory_id,
                    ];

                    if ($record->type === 'structured') {
                        $detail = $record->structured->only([
                            'title',
                            'academic_program_name',
                            'semester',
                            'student_count',
                            'group_count',
                        ]);
                    } else {
                        $detail = $record->unstructured->only([
                            'project_type',
                            'academic_program',
                            'semester',
                            'applicants',
                            'research_name',
                            'advisor',
                            'equipment',
                        ]);
                    }

                    $form->fill(array_merge($base, $detail));
                })
                ->action(function (Schedule $record, array $data) {
                    $end = Carbon::parse($data['end_at']);
                    if ($end->hour >= 16) {
                        Notification::make()
                            ->title('Hora inválida')
                            ->body('La hora de finalización no puede ser después de las 16:00.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->update([
                        'start_at'      => $data['start_at'],
                        'end_at'        => $data['end_at'],
                        'color'         => $data['color'] ?? $record->color,
                        'laboratory_id' => $data['laboratory_id'],
                    ]);

                    if ($record->type === 'structured') {
                        $record->structured()->update([
                            'title'                 => $data['title'],
                            'academic_program_name' => $data['academic_program_name'],
                            'semester'              => $data['semester'],
                            'student_count'         => $data['student_count'],
                            'group_count'           => $data['group_count'],
                        ]);
                    } else {
                        $record->unstructured()->update([
                            'project_type'     => $data['project_type'],
                            'academic_program' => $data['academic_program'],
                            'semester'         => $data['semester'],
                            'applicants'       => $data['applicants'],
                            'research_name'    => $data['research_name'],
                            'advisor'          => $data['advisor'],
                            'equipment'        => $data['equipment'] ?? null,
                        ]);
                    }
                }),

            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn(?Schedule $record) => $record instanceof Schedule && (
                    ($role === 'COORDINADOR' && $record->type === 'structured') ||
                    (! Auth::user()->hasRole('COORDINADOR') && $record->type === 'unstructured')
                ))
                ->before(fn(Schedule $r) => tap(
                    $r->{$r->type}(),
                    fn($q) => $q->delete()
                )->getModel()->delete()),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            //
            // 1) PRÁCTICA ESTRUCTURADA — SOLO COORDINADOR
            //
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

            //
            // 2) PRÁCTICA NO ESTRUCTURADA — TODOS MENOS COORDINADOR
            //
            Section::make('PRÁCTICA NO ESTRUCTURADA')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(4)
                ->schema([
                    // Tres opciones en línea, pero ocupa todo el ancho
                    Radio::make('project_type')
                        ->label('Proyecto integrador / Trabajo de grado / Investigación profesoral')
                        ->options([
                            'Trabajo de grado'         => 'Trabajo de grado',
                            'Investigación profesoral' => 'Investigación profesoral',
                        ])
                        ->columns(3)
                        ->columnSpan(4)
                        ->required(),

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

            //
            // 3) MATERIALES Y EQUIPOS
            //
            Section::make('MATERIALES Y EQUIPOS')
                ->visible(fn() => ! Auth::user()->hasRole('COORDINADOR'))
                ->columns(1)
                ->schema([
                    Select::make('products')
                        ->label('Productos disponibles')
                        ->multiple()
                        ->reactive()
                        ->options(
                            fn(callable $get) =>
                            $get('laboratory_id')
                                ? \App\Models\Product::where('laboratory_id', $get('laboratory_id'))->pluck('name', 'id')
                                : []
                        )
                        ->searchable()
                        ->required(),
                ]),

            //
            // 4) HORARIO (COMÚN)
            //
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
