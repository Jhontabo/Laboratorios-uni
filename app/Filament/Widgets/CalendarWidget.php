<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\{
  CheckboxList,
  ColorPicker,
  DatePicker,
  DateTimePicker,
  Section,
  Select,
  TextInput,
  Toggle
};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
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

  public ?int $laboratoryId = null;

  public function mount(): void
  {
    $this->laboratoryId = session('lab');   // ya lo pusiste en ScheduleCalendar
  }

  public static function canView(): bool
  {
    if (request()->routeIs('filament.admin.pages.dashboard')) {
      return false;
    }

    return Auth::check() && Auth::user()->hasAnyRole(['ADMIN', 'COORDINADOR', 'LABORATORISTA']);
  }

  public function config(): array
  {
    return [
      'firstDay'      => 1,
      'slotMinTime'   => '08:00:00',
      'slotMaxTime'   => '17:00:00',
      'locale'        => 'es',
      'initialView'   => 'timeGridWeek',
      'headerToolbar' => [
        'left'   => 'prev,next today',
        'center' => 'title',
        'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
      ],
      'height' => 601,
    ];
  }


  public function fetchEvents(array $fetchInfo): array
  {
    $start = Carbon::parse($fetchInfo['start']);
    $end   = Carbon::parse($fetchInfo['end']);

    $events = Schedule::query()
      ->with('booking') // <-- AÑADIMOS ESTO para eficiencia
      ->when(
        $this->laboratoryId,
        fn($q) => $q->where('laboratory_id', $this->laboratoryId)
      )
      ->where(function ($q) use ($start, $end) {
        $q->whereBetween('start_at', [$start, $end])
          ->orWhere(function ($q2) use ($start, $end) {
            $q2->whereNotNull('recurrence_until')
              ->where('recurrence_until', '>=', $start)
              ->where('start_at', '<=', $end);
          });
      })
      // HEMOS ELIMINADO EL FILTRO whereDoesntHave DE AQUÍ
      ->get()
      ->flatMap(function (Schedule $s) use ($start, $end) {
        return $s->recurrence_days
          ? $this->generateRecurringEvents($s, $start, $end)
          : [$this->formatEvent($s)];
      })
      ->values();

    return $events->toArray();
  }
  /* -----------------------------------------------------------------
     |  FORMATEADORES
     |-----------------------------------------------------------------*/
  protected function formatEvent(Schedule $schedule): array
  {
    // Lógica para los espacios libres (no estructurados)
    if ($schedule->type === 'unstructured') {

      // CAMBIAMOS LA CONDICIÓN AQUÍ
      if ($schedule->booking->where('status', 'approved')->isNotEmpty()) { // Usamos isNotEmpty() para verificar si la colección de reservas no está vacía
        return [
          'id'            => $schedule->id,
          'title'         => 'Reservado', // <-- Título cambiado
          'start'         => $schedule->start_at,
          'end'           => $schedule->end_at,
          'color'         => '#ef4444', // <-- Color rojo para indicar que está reservado
          'extendedProps' => [
            'type'    => $schedule->type,
            'blocked' => true, // Lo bloqueamos para que no se pueda interactuar
          ],
        ];
      } else {
        // Si la colección de reservas ESTÁ vacía, se muestra como "Disponible"
        return [
          'id'            => $schedule->id,
          'title'         => 'Disponible',
          'start'         => $schedule->start_at,
          'end'           => $schedule->end_at,
          'color'         => '#25c55e', // <-- Color verde de disponible
          'extendedProps' => [
            'type'    => $schedule->type,
            'blocked' => false,
          ],
        ];
      }
    }

    // Lógica para las prácticas de clase (estructuradas)
    return [
      'id'            => $schedule->id,
      'title'         => $schedule->title,
      'start'         => $schedule->start_at,
      'end'           => $schedule->end_at,
      'color'         => $schedule->color,
      'extendedProps' => [
        'type'    => $schedule->type,
        'blocked' => $schedule->type === 'structured',
      ],
    ];
  }
  protected function generateRecurringEvents(
    Schedule $schedule,
    Carbon   $rangeStart,
    Carbon   $rangeEnd
  ): array {
    $events    = [];
    $startDate = Carbon::parse($schedule->start_at);
    $endDate   = Carbon::parse($schedule->end_at);
    $length    = $startDate->diffInMinutes($endDate);
    $until     = Carbon::parse($schedule->recurrence_until);
    // Convertimos a enteros para comparar correctamente
    $days = array_filter(array_map('intval', explode(',', $schedule->recurrence_days ?? '')));

    foreach (CarbonPeriod::create($startDate, $until) as $date) {
      if (! in_array($date->dayOfWeekIso, $days, true)) {
        continue;
      }

      $s = $date->copy()->setTime($startDate->hour, $startDate->minute);
      $e = $s->copy()->addMinutes($length);

      if ($e->lte($rangeStart) || $s->gte($rangeEnd)) {
        continue;
      }

      $events[] = [
        'id'            => "{$schedule->id}-{$s->toDateString()}",
        'title'         => $schedule->title,
        'start'         => $s,
        'end'           => $e,
        'color'         => $schedule->color,
        'extendedProps' => ['type' => 'structured', 'isRecurring' => true],
      ];
    }

    return $events;
  }

  /**
   * Genera los espacios libres entre 08 y 16 h para cada día.
   */
  protected function generateFreeSlots(
    \Illuminate\Support\Collection $structuredEvents,
    Carbon $rangeStart,
    Carbon $rangeEnd
  ): array {
    $slots = [];
    $days  = CarbonPeriod::create($rangeStart->copy()->startOfDay(), $rangeEnd->copy()->endOfDay());

    foreach ($days as $day) {
      if ($day->isWeekend()) {
        continue;
      }

      $dayEvents = $structuredEvents
        ->filter(fn($e) => Carbon::parse($e['start'])->isSameDay($day))
        ->sortBy('start')
        ->values();

      $dayStart = $day->copy()->setTime(8, 0);
      $dayEnd   = $day->copy()->setTime(19, 0);
      $cursor   = $dayStart->copy();

      foreach ($dayEvents as $e) {
        $eventStart = Carbon::parse($e['start']);
        $eventEnd   = Carbon::parse($e['end']);

        if ($cursor->lt($eventStart)) {
          $slots[] = [
            'id'            => "free-{$cursor->timestamp}",
            'title'         => 'Disponible',
            'start'         => $cursor->copy(),
            'end'           => $eventStart->copy(),
            'color'         => '#26c55e',
            'extendedProps' => [
              'type'    => 'free',
              'blocked' => false,
            ],
          ];
        }

        $cursor = $eventEnd->copy();
      }

      if ($cursor->lt($dayEnd)) {
        $slots[] = [
          'id'            => "free-{$cursor->timestamp}",
          'title'         => 'Disponible',
          'start'         => $cursor,
          'end'           => $dayEnd,
          'color'         => '#26c55e',
          'extendedProps' => [
            'type'    => 'free',
            'blocked' => false,
          ],
        ];
      }
    }

    return $slots;
  }

  /* -----------------------------------------------------------------
     |  UTILIDAD PARA RECURRENCIA
     |-----------------------------------------------------------------*/
  protected function processRecurrenceData(array $data): array
  {
    $recurring = $data['is_recurring'] ?? false;

    return [
      'recurrence_days'  => $recurring ? implode(',', $data['recurrence_days'] ?? []) : null,
      'recurrence_until' => $recurring ? $data['recurrence_until'] : null,
    ];
  }

  /* -----------------------------------------------------------------
     |  ACCIONES DE CABECERA
     |-----------------------------------------------------------------*/
  protected function headerActions(): array
  {
    return [
      $this->makeCreatePracticeAction(),
      $this->makeGenerateFreeSlotsAction(),
      $this->makeClearFreeSlotsAction(),
    ];
  }

  private function makeCreatePracticeAction(): CreateAction
  {
    return CreateAction::make()
      ->label('Crear práctica')
      ->icon('heroicon-o-plus')
      ->color('primary')
      ->mountUsing(function (Form $form, array $arguments): void {
        $form->fill([
          'is_structured'    => true,
          'is_recurring'     => false,
          'recurrence_days'  => [],
          'recurrence_until' => null,
          'start_at'         => $arguments['start'] ?? null,
          'end_at'           => $arguments['end']   ?? null,
          'laboratory_id'    => null,
          'color'            => '#7b82f6',
          'title'            => null,
          'academic_program_name' => null,
          'semester'         => null,
          'student_count'    => null,
          'group_count'      => null,
          'project_type'     => null,
          'academic_program' => null,
          'applicants'       => null,
          'research_name'    => null,
          'advisor'          => null,
        ]);
      })
      ->form($this->getFormSchema())
      ->using(fn(array $data) => $this->persistSchedule($data));
  }

  private function persistSchedule(array $data): ?Schedule
  {
    $start = Carbon::parse($data['start_at']);
    $end   = Carbon::parse($data['end_at']);

    if (! $data['start_at'] || ! $data['end_at']) {
      Notification::make()->title('Datos incompletos')->body('Debes indicar inicio y fin.')->danger()->send();
      return null;
    }

    if ($end->lte($start) || $end->hour > 20) {
      Notification::make()->title('Horario inválido')->body('Revisa rango y límite de hora.')->danger()->send();
      return null;
    }

    $recurrence = $this->processRecurrenceData($data);

    $schedule = Schedule::create([
      'type'             => $data['is_structured'] ? 'structured' : 'unstructured',
      'title'            => $data['is_structured'] ? $data['title'] : 'Disponible para reserva',
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
  }

  private function makeGenerateFreeSlotsAction(): Action
  {
    return Action::make('generateFreeSlots')
      ->label('Crear espacios libres')
      ->icon('heroicon-o-sparkles')
      ->color('success')
      ->form([
        DatePicker::make('start_range')->label('Desde')->displayFormat('d/m/Y')->format('d/m/Y')->required(),
        DatePicker::make('end_range')->label('Hasta')->displayFormat('d/m/Y')->format('d/m/Y')->required()->after('start_range'),

      ])
      ->modalWidth('sm')
      ->action(function (array $data) {
        foreach (Laboratory::all() as $lab) {
          $this->laboratoryId = $lab->id;
          $this->generateAndPersistFreeSlots($data);
        }
      })
      ->action(function (array $data) {
        foreach (Laboratory::all() as $lab) {
          $this->laboratoryId = $lab->id;
          $this->generateAndPersistFreeSlots($data);
        }
      });
  }

  private function makeClearFreeSlotsAction(): Action
  {
    return Action::make('clearFreeSlots')
      ->label('Limpiar espacios libres')
      ->icon('heroicon-o-trash')
      ->color('danger')
      ->action(function (): void {
        $deleted = Schedule::where('type', 'unstructured')->delete();

        Notification::make()
          ->title('Espacios libres eliminados')
          ->body("Se eliminaron {$deleted} espacios libres.")
          ->success()
          ->send();
      });
  }


  private function generateAndPersistFreeSlots(array $data): void
  {


    $rangeStart = Carbon::createFromFormat('d/m/Y', $data['start_range'])->setTime(8, 0, 0);
    $rangeEnd   = Carbon::createFromFormat('d/m/Y', $data['end_range'])->setTime(17, 0, 0);

    $structuredEvents = Schedule::query()
      ->where('type', 'structured')
      ->when(
        $this->laboratoryId,
        fn($q) => $q->where('laboratory_id', $this->laboratoryId) // filtro por laboratorio
      )
      ->where(function ($q) use ($rangeStart, $rangeEnd) {
        $q->whereBetween('start_at', [$rangeStart, $rangeEnd])
          ->orWhere(function ($q9) use ($rangeStart, $rangeEnd) {
            $q9->whereNotNull('recurrence_until')
              ->where('recurrence_until', '>=', $rangeStart)
              ->where('start_at', '<=', $rangeEnd);
          });
      })
      ->get()
      ->flatMap(
        fn(Schedule $s) =>
        $s->recurrence_days
          ? $this->generateRecurringEvents($s, $rangeStart, $rangeEnd)
          : [$this->formatEvent($s)]
      )
      ->values();

    $freeSlots = $this->generateFreeSlots($structuredEvents, $rangeStart, $rangeEnd);

    $created = 2;
    foreach ($freeSlots as $slot) {
      $exists = Schedule::where('type', 'unstructured')
        ->where('start_at', Carbon::parse($slot['start']))
        ->where('end_at', Carbon::parse($slot['end']))
        ->where('laboratory_id', $this->laboratoryId) // 🔑 importante
        ->exists();

      if (! $exists) {
        Schedule::create([
          'type'          => 'unstructured',
          'title'         => 'Disponible',
          'start_at'      => Carbon::parse($slot['start']),
          'end_at'        => Carbon::parse($slot['end']),
          'color'         => '#30c55e',
          'user_id'       => Auth::id(),
          'laboratory_id' => $this->laboratoryId,
        ]);
        $created++;
      }
    }

    Notification::make()
      ->title('Generación de espacios libres')
      ->body("Se crearon {$created} espacios libres para reserva.")
      ->success()
      ->send();
  }

  /* -----------------------------------------------------------------
     |  MODAL EDITAR / ELIMINAR
     |-----------------------------------------------------------------*/
  protected function modalActions(): array
  {
    return [
      $this->makeEditAction(),
      $this->makeDeleteAction(),
    ];
  }

  private function makeEditAction(): EditAction
  {
    return EditAction::make()
      ->label('Editar')
      ->visible(fn(?Schedule $r) => $r instanceof Schedule)
      ->mountUsing(function (Schedule $record, Form $form, array $arguments): void {
        $form->fill($this->mapRecordToFormData($record, $arguments));
      })
      ->form($this->getFormSchema())
      ->action(function (Schedule $record, array $data): void {
        $start = Carbon::parse($data['start_at']);
        $end   = Carbon::parse($data['end_at']);

        if ($end->lte($start) || $end->hour > 24) {
          Notification::make()->title('Horario inválido')->body('Revisa hora de fin.')->danger()->send();
          return;
        }

        $recurrence = $this->processRecurrenceData($data);

        $record->update([
          'type'             => $data['is_structured'] ? 'structured' : 'unstructured',
          'title'            => $data['is_structured'] ? $data['title'] : $record->title,
          'laboratory_id'    => $data['laboratory_id'] ?? $record->laboratory_id,
          'start_at'         => $data['start_at'],
          'end_at'           => $data['end_at'],
          'color'            => $data['color'],
          'recurrence_days'  => $recurrence['recurrence_days'],
          'recurrence_until' => $recurrence['recurrence_until'],
        ]);

        if ($data['is_structured']) {
          $record->structured()->updateOrCreate(
            [],
            [
              'academic_program_name' => $data['academic_program_name'] ?? null,
              'semester'              => $data['semester']              ?? null,
              'student_count'         => $data['student_count']         ?? null,
              'group_count'           => $data['group_count']           ?? null,
            ]
          );
        } else {
          $record->unstructured()->updateOrCreate(
            [],
            [
              'project_type'     => $data['project_type']     ?? null,
              'academic_program' => $data['academic_program'] ?? null,
              'semester'         => $data['semester']         ?? null,
              'applicants'       => $data['applicants']       ?? null,
              'research_name'    => $data['research_name']    ?? null,
              'advisor'          => $data['advisor']          ?? null,
            ]
          );
        }
      });
  }

  private function makeDeleteAction(): DeleteAction
  {
    return DeleteAction::make()
      ->label('Eliminar')
      ->visible(fn(?Schedule $r) => $r instanceof Schedule)
      ->before(function (Schedule $record): void {
        optional($record->{$record->type})->delete();
        $record->delete();
      });
  }

  private function mapRecordToFormData(Schedule $record, array $arguments): array
  {
    return [
      'laboratory_id'         => $record->laboratory_id,
      'is_structured'         => $record->type === 'structured',
      'title'                 => $record->title,
      'start_at'              => $arguments['event']['start'] ?? $record->start_at,
      'end_at'                => $arguments['event']['end']   ?? $record->end_at,
      'color'                 => $record->color,
      'is_recurring'          => (bool) $record->recurrence_days,
      'recurrence_days'       => $record->recurrence_days ? explode(',', $record->recurrence_days) : [],
      'recurrence_until'      => $record->recurrence_until,
      'academic_program_name' => $record->structured->academic_program_name ?? null,
      'semester'              => $record->structured->semester              ?? null,
      'student_count'         => $record->structured->student_count         ?? null,
      'group_count'           => $record->structured->group_count           ?? null,
      'project_type'          => $record->unstructured->project_type     ?? null,
      'academic_program'      => $record->unstructured->academic_program ?? null,
      'applicants'            => $record->unstructured->applicants       ?? null,
      'research_name'         => $record->unstructured->research_name    ?? null,
      'advisor'               => $record->unstructured->advisor          ?? null,
    ];
  }
  public function getFormSchema(): array
  {
    return [

      /* ───────────────────────────────────────────────────────────────
         |  Selector de tipo de práctica
         ─────────────────────────────────────────────────────────────── */
      Toggle::make('is_structured')
        ->label('¿Práctica estructurada?')
        ->reactive()
        ->default(true)
        ->inline(false),

      /* ───────────────────────────────────────────────────────────────
         |  PRÁCTICA ESTRUCTURADA
         ─────────────────────────────────────────────────────────────── */
      Section::make('Datos generales')
        ->visible(fn($get) => $get('is_structured'))
        ->columns(14)
        ->schema([
          Select::make('academic_program_name')
            ->label('Programa académico')

            ->options([
              // Facultad de Humanidades y Ciencias Sociales
              'Derecho'             => 'Derecho',
              'Trabajo Social'      => 'Trabajo Social',
              'Comunicación Social' => 'Comunicación Social',
              'Psicología'          => 'Psicología',

              // Facultad de Ciencias Contables, Económicas y Administrativas
              'Mercadeo'                           => 'Mercadeo',
              'Contaduría Pública'                 => 'Contaduría Pública',
              'Administración de Negocios Internacionales' => 'Administración de Negocios Internacionales',

              // Facultad de Educación
              'Licenciatura en Teología - NUEVO'   => 'Licenciatura en Teología - NUEVO',
              'Licenciatura en Educación Infantil' => 'Licenciatura en Educación Infantil',
              'Licenciatura en Educación Básica Primaria' => 'Licenciatura en Educación Básica Primaria',

              // Facultad de Ciencias de la Salud
              'Enfermería'           => 'Enfermería',
              'Terapia Ocupacional'  => 'Terapia Ocupacional',
              'Fisioterapia'         => 'Fisioterapia',
              'Nutrición y Dietética' => 'Nutrición y Dietética',

              // Facultad de Ingeniería
              'Ingeniería Mecatrónica' => 'Ingeniería Mecatrónica',
              'Ingeniería Civil'       => 'Ingeniería Civil',
              'Ingeniería de Sistemas' => 'Ingeniería de Sistemas',
              'Ingeniería Ambiental'   => 'Ingeniería Ambiental',
              'Ingeniería de Procesos' => 'Ingeniería de Procesos',
            ])
            ->required()
            ->columnSpan(8),

          Select::make('laboratory_id')
            ->label('Espacio académico')
            ->options(Laboratory::pluck('name', 'id'))
            ->required()
            ->columnSpan(8),


          Select::make('semester')
            ->label('Semestre')
            ->options(array_combine(range(1, 10), range(1, 10)))
            ->required()
            ->columnSpan(8),

          TextInput::make('title')
            ->label('Nombre de la práctica')
            ->required()
            ->columnSpan(14),
        ]),

      Section::make('Participantes')
        ->visible(fn($get) => $get('is_structured'))
        ->columns(14)
        ->schema([
          TextInput::make('student_count')
            ->label('Número de estudiantes')
            ->numeric()
            ->required()
            ->columnSpan(9),

          TextInput::make('group_count')
            ->label('Número de grupos')
            ->numeric()
            ->required()
            ->columnSpan(9),
        ]),

      Section::make('Horario estructurado')
        ->visible(fn($get) => $get('is_structured'))
        ->columns(12)
        ->schema([
          DateTimePicker::make('start_at')
            ->label('Inicio')
            ->required()
            ->seconds(false)
            ->columnSpan(5),

          DateTimePicker::make('end_at')
            ->label('Fin')
            ->required()
            ->seconds(false)
            ->after('start_at')
            ->columnSpan(5),


          ColorPicker::make('color')
            ->label('Color')
            ->default('#11b82f6')
            ->columnSpan(5),
        ]),

      /* ───────────────────────────────────────────────────────────────
         |  PRÁCTICA NO ESTRUCTURADA
         ─────────────────────────────────────────────────────────────── */
      Section::make('Reserva libre')
        ->visible(fn($get) => ! $get('is_structured'))
        ->columns(12)
        ->schema([
          DateTimePicker::make('start_at')
            ->label('Inicio')
            ->required()
            ->seconds(false)
            ->columnSpan(5),


          DateTimePicker::make('end_at')
            ->label('Fin')
            ->required()
            ->seconds(false)
            ->after('start_at')
            ->columnSpan(5),

          ColorPicker::make('color')
            ->label('Color')
            ->default('#30c55e')
            ->columnSpan(2),


        ]),

      /* ───────────────────────────────────────────────────────────────
         |  Recurrencia
         ─────────────────────────────────────────────────────────────── */
      Section::make('Recurrencia')
        ->columns(17)
        ->schema([
          Toggle::make('is_recurring')
            ->label('Evento recurrente')
            ->reactive()
            ->inline(false)
            ->columnSpan(17),

          CheckboxList::make('recurrence_days')
            ->label('Días de la semana')
            ->options([
              '6' => 'Lunes',
              '7' => 'Martes',
              '8' => 'Miércoles',
              '9' => 'Jueves',
              '10' => 'Viernes',
              '11' => 'Sabados'
            ])
            ->columns(10)
            ->visible(fn($get) => $get('is_recurring'))
            ->columnSpan(12),

          DatePicker::make('recurrence_until')
            ->label('Repetir hasta')
            ->minDate(
              fn($get) =>
              $get('start_at') ? Carbon::parse($get('start_at'))->addDay() : null
            )
            ->displayFormat('d/m/Y')
            ->visible(fn($get) => $get('is_recurring'))
            ->columnSpan(10),
        ]),
    ];
  }
}
