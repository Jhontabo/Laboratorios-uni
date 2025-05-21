<?php

namespace App\Filament\Widgets;

use App\Models\AcademicProgram;
use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
        return !in_array(request()->route()->getName(), [
            'filament.admin.pages.dashboard',
        ]);
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
            'editable' => true,
            'eventDurationEditable' => true,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $query = Schedule::query()
            ->where('type', 'structured')
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']]);

        if ($labId = session()->get('lab')) {
            $query->where('laboratory_id', $labId);
        }

        return $query->get()->map(function (Schedule $schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->start_at,
                'end' => $schedule->end_at,
                'color' => $schedule->color,
            ];
        })->toArray();
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
                ->form($this->getFormSchema())
                ->using(function (array $data, string $model): Model {
                    \DB::beginTransaction();
                    try {
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
                            'academic_program_name' => $data['academic_program_name'], // Cambiado de academic_program_id a academic_program_name
                            'semester' => $data['semester'],
                            'student_count' => $data['student_count'],
                            'group_count' => $data['group_count'],
                        ]);

                        if (!empty($data['products'])) {
                            $schedule->products()->sync($data['products']);
                        }

                        \DB::commit();
                        return $schedule;
                    } catch (\Exception $e) {
                        \DB::rollBack();
                        throw $e;
                    }
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
                        ->live()
                        ->afterStateUpdated(fn($state, $set) => $set('products', [])),

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
                ->columns(4),

            Section::make('Detalles Académicos')
                ->schema([
                    Select::make('academic_program_name')
                        ->label('Programa Académico')
                        ->options([
                            'Ingeniería de Sistemas' => 'Ingeniería de Sistemas',
                            'Ingeniería Industrial' => 'Ingeniería Industrial',
                            'Contaduría Pública' => 'Contaduría Pública',
                            'Administración de Empresas' => 'Administración de Empresas',
                            // ... agrega más según tu necesidad
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
                        ->minValue(3)
                        ->maxValue(102)
                        ->required(),

                    TextInput::make('group_count')
                        ->label('Número de Grupos')
                        ->numeric()
                        ->minValue(3)
                        ->maxValue(22)
                        ->required(),
                ])
                ->columns(4),

            Section::make('Configuración del Horario')
                ->schema([
                    TimePicker::make('start_at')
                        ->label('Hora de Inicio')
                        ->required()
                        ->seconds(false),

                    TimePicker::make('end_at')
                        ->label('Hora de Finalización')
                        ->required()
                        ->seconds(false)
                        ->after('start_at'),

                    ColorPicker::make('color')
                        ->label('Color del Evento')
                        ->default('#5b82f6'),
                ])
                ->columns(4),


        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    if (!$record->structured) {
                        throw new \Exception('Registro de práctica estructurada no encontrado');
                    }

                    $form->fill([
                        'title' => $record->title,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'color' => $record->color,
                        'laboratory_id' => $record->laboratory_id,
                        'user_id' => $record->user_id,
                        'academic_program_name' => $record->structured->academic_program_name, // Cambiado de academic_program_id a academic_program_name
                        'semester' => $record->structured->semester,
                        'student_count' => $record->structured->student_count,
                        'group_count' => $record->structured->group_count,
                        'products' => $record->products->pluck('id')->toArray(),
                    ]);
                })
                ->action(function (Schedule $record, array $data) {
                    \DB::beginTransaction();
                    try {
                        $record->update([
                            'title' => $data['title'],
                            'start_at' => $data['start_at'],
                            'end_at' => $data['end_at'],
                            'color' => $data['color'] ?? $record->color,
                            'laboratory_id' => $data['laboratory_id'],
                            'user_id' => $data['user_id'],
                        ]);

                        $record->structured()->update([
                            'academic_program_name' => $data['academic_program_name'], // Usar academic_program_name en lugar de academic_program_id
                            'semester' => $data['semester'],
                            'student_count' => $data['student_count'],
                            'group_count' => $data['group_count'],
                        ]);


                        $record->products()->sync($data['products'] ?? []);

                        \DB::commit();
                    } catch (\Exception $e) {
                        \DB::rollBack();
                        throw $e;
                    }
                }),

            DeleteAction::make()
                ->before(function (Schedule $record) {
                    $record->structured()->delete();
                    $record->products()->detach();
                }),
        ];
    }
}
