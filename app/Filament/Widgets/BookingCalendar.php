<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookingCalendar extends FullCalendarWidget
{
    public Model|string|null $model = Schedule::class;
    protected string $viewId = 'booking-calendar';
    // public static function canView(): bool
    // {
    //     return !request()->routeIs('filament.admin.pages.dashboard');
    // }

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
            'editable'              => false,
            'droppable'             => false,
            'weekends' => false,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        // El id debe ser siempre el id puro del modelo
        return Schedule::where('type', 'unstructured')
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(fn(Schedule $schedule) => [
                'id' => $schedule->id, // <= solo el número
                'title' => $schedule->title,
                'start' => $schedule->start_at,
                'end' => $schedule->end_at,
                'color' => $schedule->color,
            ])
            ->toArray();
    }

    protected function getDefaultEventHandlers(): array
    {
        return array_merge(parent::getDefaultEventHandlers(), [
            'eventDrop' => 'handleUnstructuredDrop',
        ]);
    }

    public function handleUnstructuredDrop(array $event, array $delta): void
    {
        $schedule = Schedule::find($event['id']);
        if (! $schedule || $schedule->type !== 'unstructured') {
            return;
        }
        $schedule->update([
            'start_at' => $event['start'],
            'end_at'   => $event['end'],
        ]);
        Notification::make()
            ->title('Reserva actualizada')
            ->success()
            ->send();
        $this->dispatchBrowserEvent('refreshCalendar'); // o el método que uses para recargar
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Reserva')
                ->mountUsing(function ($record, Form $form, array $arguments) {
                    if (! $record instanceof Schedule) {
                        Notification::make()
                            ->title('Reserva no encontrada')
                            ->body('No se pudo cargar la reserva. Tal vez fue eliminada o hay un error de sincronización.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record->loadMissing('unstructured');

                    $form->fill([
                        'start_at'        => $arguments['event']['start'] ?? $record->start_at,
                        'end_at'          => $arguments['event']['end']   ?? $record->end_at,
                        'color'           => $record->color,
                        'laboratory_id'   => $record->laboratory_id,
                        'user_id'         => $record->user_id,
                        'project_type'    => $record->unstructured->project_type   ?? '',
                        'academic_program' => $record->unstructured->academic_program ?? '',
                        'semester'        => $record->unstructured->semester       ?? null,
                        'applicants'      => $record->unstructured->applicants     ?? '',
                        'research_name'   => $record->unstructured->research_name  ?? '',
                        'advisor'         => $record->unstructured->advisor        ?? '',
                        'products'        => $record->products->pluck('id')->toArray(),
                    ]);
                })
                ->form($this->getFormSchema())
                ->action(function ($record, array $data) {
                    // 1) Es Schedule válido?
                    if (! $record instanceof Schedule) {
                        Notification::make()
                            ->title('Reserva no encontrada')
                            ->body('No se pudo actualizar porque no existe.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // 2) Validación de hora de fin <= 16:00
                    if (Carbon::parse($data['end_at'])->format('H') > 16) {
                        Notification::make()
                            ->title('Hora inválida')
                            ->body('La hora de finalización no puede ser posterior a las 16:00.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record->update([
                        'title'         => 'Reserva', // siempre fija
                        'start_at'      => $data['start_at'],
                        'end_at'        => $data['end_at'],
                        'color'         => $data['color'] ?? '#3b82f6',
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id'       => $data['user_id'],
                    ]);

                    $record->unstructured()->updateOrCreate(
                        ['schedule_id' => $record->id],
                        [
                            'project_type'    => $data['project_type'],
                            'academic_program' => $data['academic_program'],
                            'semester'        => $data['semester'],
                            'applicants'      => $data['applicants'],
                            'research_name'   => $data['research_name'],
                            'advisor'         => $data['advisor'],
                        ]
                    );

                    $record->products()->sync($data['products'] ?? []);
                }),

            DeleteAction::make()
                ->label('Eliminar Reserva')
                ->before(function ($record) {
                    if (! $record instanceof Schedule) {
                        return;
                    }
                    $record->unstructured?->delete();
                    $record->products()->detach();
                }),
        ];
    }
    public function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Reservar espacio')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form($this->getFormSchema())
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at' => $arguments['end'] ?? null,
                    ]);
                })
                ->using(function (array $data): ?Model {
                    $startDay = Carbon::parse($data['start_at'])->dayOfWeek;
                    if (in_array($startDay, [0, 6])) {
                        Notification::make()
                            ->title('No se pueden crear prácticas los fines de semana.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    $schedule = \App\Models\Schedule::create([
                        'title' => 'Reserva', // ← SIEMPRE RESERVA
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? '#3b82f6',
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                        'type' => 'unstructured',
                    ]);
                    $schedule->unstructured()->create([
                        'project_type' => $data['project_type'],
                        'academic_program' => $data['academic_program'],
                        'semester' => $data['semester'],
                        'applicants' => $data['applicants'],
                        'research_name' => $data['research_name'],
                        'advisor' => $data['advisor'],
                    ]);
                    $schedule->products()->sync($data['products'] ?? []);
                    return $schedule->load('unstructured');
                }),
        ];
    }
    public function getFormSchema(): array
    {
        return [
            Section::make('Información General')
                ->schema([
                    Select::make('laboratory_id')
                        ->label('Laboratorio')
                        ->options(Laboratory::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->reactive(),
                    Select::make('user_id')
                        ->label('Responsable')
                        ->options(
                            \App\Models\User::role('docente')
                                ->get()
                                ->mapWithKeys(fn($user) => [
                                    $user->id => "{$user->name} {$user->last_name}"
                                ])
                        )
                        ->searchable()
                        ->required(),
                    Select::make('academic_program')
                        ->label('Programa Académico')
                        ->options([
                            'Ingeniería de Sistemas' => 'Ingeniería de Sistemas',
                            'Ingeniería Industrial' => 'Ingeniería Industrial',
                            'Contaduría Pública' => 'Contaduría Pública',
                            'Administración de Empresas' => 'Administración de Empresas',
                        ])
                        ->required(),
                ])
                ->columns(4),
            Section::make('Detalles de la Práctica')
                ->schema([
                    Select::make('project_type')
                        ->label('Tipo de Proyecto')
                        ->options([
                            'Trabajo de grado' => 'Trabajo de grado',
                            'Investigación profesoral' => 'Investigación profesoral',
                        ])
                        ->required(),
                    TextInput::make('research_name')
                        ->label('Nombre de la investigación')
                        ->required(),
                    TextInput::make('advisor')
                        ->label('Nombre del asesor')
                        ->required(),
                    TextInput::make('applicants')
                        ->label('Solicitantes')
                        ->required(),
                    Select::make('semester')
                        ->label('Semestre')
                        ->options(collect(range(2, 10))->mapWithKeys(fn($item) => [$item => (string)$item]))
                        ->required()
                        ->native(false),
                    Select::make('products')
                        ->label('Productos disponibles')
                        ->multiple()
                        ->reactive()
                        ->options(function (callable $get) {
                            $labId = $get('laboratory_id');
                            if (!$labId) return [];
                            return \App\Models\Product::where('laboratory_id', $labId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                ])
                ->columns(3),
            Section::make('Horario')
                ->schema([
                    DateTimePicker::make('start_at')
                        ->label('Inicio')
                        ->required()
                        ->seconds(false),
                    DateTimePicker::make('end_at')
                        ->label('Finalización')
                        ->required()
                        ->seconds(false)
                        ->after('start_at'),
                    ColorPicker::make('color')
                        ->label('Color del Evento')
                        ->default('#4b82f6'),
                ])
                ->columns(4),
        ];
    }
}
