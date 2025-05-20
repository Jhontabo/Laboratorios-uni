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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
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
        $routesToHideWidget = [
            'filament.admin.pages.dashboard',
        ];

        return !in_array(request()->route()->getName(), $routesToHideWidget);
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
            'editable' => true, // Habilitar edición
            'droppable' => true, // Habilitar arrastrar y soltar
            'eventDurationEditable' => true,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $labId = session()->get('lab');
        $query = Schedule::query();

        $query->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']]);

        if (!is_null($labId)) {
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


    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $form->fill([
                        'title' => $record->title,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'color' => $record->color,
                        'is_available' => $record->is_available,
                        'laboratory_id' => $record->laboratory_id,
                        'academic_program_id' => $record->academic_program_id,
                        'semester' => $record->semester,
                        'user_id' => $record->user_id,
                        'student_count' => $record->student_count,
                        'group_count' => $record->group_count,
                        'products' => $record->products->pluck('id')->toArray(), // Añadir productos seleccionados
                    ]);
                })
                ->action(function (Schedule $record, array $data) {
                    // Extraer productos antes de actualizar
                    $products = $data['products'] ?? [];
                    unset($data['products']);

                    // Actualizar el registro principal
                    $record->update($data);

                    // Sincronizar los productos
                    $record->products()->sync($products);
                }),
            DeleteAction::make(),
        ];
    }


    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Horario')  // Cambiar el texto del botón
                ->icon('heroicon-o-plus') // Agregar icono
                ->color('primary')        // Cambiar color
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at' => $arguments['end'] ?? null,
                    ]);
                })
                ->form($this->getFormSchema()) // Asegurar que use el schema correcto
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Información Académica')
                ->schema([
                    Select::make('academic_program_id')
                        ->label('Programa Académico')
                        ->relationship('academicProgram', 'name')
                        ->getOptionLabelFromRecordUsing(fn(AcademicProgram $record) => $record->full_name)
                        ->searchable(['name', 'code'])
                        ->preload()
                        ->required(),

                    Select::make('laboratory_id')
                        ->label('Espacio academico')
                        ->options(Laboratory::pluck('name', 'id')->toArray())
                        ->required()
                        ->reactive(),

                    Select::make('semester')
                        ->label('Semestre')
                        ->options(array_combine(range(1, 10), range(1, 10)))
                        ->required()
                        ->native(false),

                    Select::make('user_id')
                        ->label('Profesor Responsable')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name', // Esto es requerido pero lo sobreescribiremos
                            modifyQueryUsing: fn(Builder $query) => $query->role('docente')
                        )
                        ->getOptionLabelFromRecordUsing(fn(User $user) => "{$user->name} {$user->last_name}")
                        ->searchable(['name', 'last_name'])
                        ->preload()
                        ->required(),

                    TextInput::make('title')
                        ->label('Nombre de la practica academica')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('student_count')
                        ->label('Número de Estudiantes')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->required(),

                    TextInput::make('group_count')
                        ->label('Número de Grupos')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(20)
                        ->required(),
                ])
                ->columns(2),

            Section::make('Detalles del Horario')
                ->schema([
                    TimePicker::make('start_at')
                        ->label('Hora de Inicio')
                        ->required()
                        ->seconds(false)
                        ->displayFormat('H:i')
                        ->native(false),

                    TimePicker::make('end_at')
                        ->label('Hora de Finalización')
                        ->required()
                        ->seconds(false)
                        ->displayFormat('H:i')
                        ->native(false)
                        ->after('start_at'),



                    /* Select::make('products') // Cambiado de product_ids a products */
                    /*     ->label('Productos') */
                    /*     ->multiple() */
                    /*     ->relationship( */
                    /*         name: 'products', */
                    /*         titleAttribute: 'name', */
                    /*         modifyQueryUsing: fn(Builder $query, Get $get) => */
                    /*         $query->where('laboratory_id', $get('laboratory_id')) */
                    /*     ) */
                    /*     ->getOptionLabelFromRecordUsing( */
                    /*         fn(Product $product) => "{$product->name} - {$product->serial_number}" */
                    /*     ) */
                    /*     ->searchable() */
                    /*     ->preload() */
                    /*     ->required() */
                ])
                ->columns(2),

            Section::make('Configuración Adicional')
                ->schema([


                    Textarea::make('description')
                        ->label('Descripción Adicional')
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Toggle::make('is_available')
                        ->label('Disponible para Reserva')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true),

                    ColorPicker::make('color')
                        ->label('Color del Evento')
                        ->default('#3b82f6'),
                ])
                ->columns(2),
        ];
    }
}
