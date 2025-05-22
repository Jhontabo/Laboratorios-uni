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
        return Schedule::where('type', 'unstructured')
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(fn(Schedule $schedule) => [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->start_at,
                'end' => $schedule->end_at,
                'color' => $schedule->color,
            ])
            ->toArray();
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $record->loadMissing('unstructured');

                    $form->fill([
                        'title' => $record->title,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'color' => $record->color,
                        'laboratory_id' => $record->laboratory_id,
                        'user_id' => $record->user_id,
                        'project_type' => $record->unstructured->project_type ?? '',
                        'academic_program' => $record->unstructured->academic_program ?? '',
                        'semester' => $record->unstructured->semester ?? null,
                        'applicants' => $record->unstructured->applicants ?? '',
                        'research_name' => $record->unstructured->research_name ?? '',
                        'advisor' => $record->unstructured->advisor ?? '',
                        'equipment' => $record->unstructured->equipment ?? '',
                        'materials' => $record->unstructured->materials ?? '',
                        'supplies' => $record->unstructured->supplies ?? '',
                    ]);
                })
                ->form($this->getFormSchema())
                ->action(function (Schedule $record, array $data) {
                    $record->update([
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'] ?? $record->color,
                        'laboratory_id' => $data['laboratory_id'],
                        'user_id' => $data['user_id'],
                    ]);

                    $record->unstructured()->updateOrCreate(
                        ['schedule_id' => $record->id],
                        [
                            'project_type' => $data['project_type'],
                            'academic_program' => $data['academic_program'],
                            'semester' => $data['semester'],
                            'applicants' => $data['applicants'],
                            'research_name' => $data['research_name'],
                            'advisor' => $data['advisor'],
                            'equipment' => $data['equipment'],
                            'materials' => $data['materials'],
                            'supplies' => $data['supplies'],
                        ]
                    );
                }),

            DeleteAction::make()
                ->before(function (Schedule $record) {
                    if ($record->unstructured) {
                        $record->unstructured()->delete();
                    }
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
                ->using(function (array $data, string $model): ?Model {
                    $startDay = Carbon::parse($data['start_at'])->dayOfWeek;

                    if (in_array($startDay, [0, 6])) {
                        Notification::make()
                            ->title('No se pueden crear prácticas los fines de semana.')
                            ->danger()
                            ->send();

                        return null;
                    }

                    $schedule = $model::create([
                        'title' => $data['title'],
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
                        'equipment' => $data['equipment'],
                        'materials' => $data['materials'],
                        'supplies' => $data['supplies'],
                    ]);

                    return $schedule->load('unstructured');
                }),
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
                        ->label('Responsable')
                        ->options(User::whereHas('roles', fn($q) => $q->where('name', 'docente'))->pluck('name', 'id'))
                        ->searchable()
                        ->required(),


                ])
                ->columns(3),

            Section::make('Detalles de la Práctica')
                ->schema([
                    Select::make('project_type')
                        ->label('Proyecto integrador')
                        ->options([
                            'Trabajo de grado' => 'Trabajo de grado',
                            'Investigación profesoral' => 'Investigación profesoral',
                        ])
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

                    TextInput::make('semester')
                        ->label('Semestre')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->required(),

                    TextInput::make('applicants')
                        ->label('Nombre de los solicitantes')
                        ->required(),

                    TextInput::make('research_name')
                        ->label('Nombre de la investigación')
                        ->required(),

                    TextInput::make('advisor')
                        ->label('Nombre del asesor')
                        ->required(),
                ])
                ->columns(2),

            Section::make('Materiales, Equipos e Insumos')
                ->schema([
                    Textarea::make('equipment')
                        ->label('Materiales')
                        ->columnSpan(1)

                ])
                ->columns(3),

            Section::make('Horario')
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
        ];
    }
}
