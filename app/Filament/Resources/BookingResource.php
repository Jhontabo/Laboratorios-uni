<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\{
    Section,
    Radio,
    Select,
    TextInput,
    DateTimePicker,
    ColorPicker
};
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Reservar Espacio';
    protected static ?string $navigationGroup = 'Gestión de Reservas';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Schedule::where('type', 'unstructured')->orderBy('start_at'))
            ->columns([
                TextColumn::make('title')->label('Título')->sortable(),
                TextColumn::make('start_at')->label('Inicio')->sortable(),
                TextColumn::make('end_at')->label('Fin')->sortable(),
                TextColumn::make('laboratory.name')->label('Laboratorio'),
            ])
            ->actions([
                Action::make('reservar')
                    ->label('Reservar')
                    ->button()
                    ->modalHeading('Solicitud de Reserva')
                    ->modalWidth('lg')
                    ->form([
                        Section::make()->schema([
                            Radio::make('project_type')
                                ->label('Poryecto integrador')
                                ->options([
                                    'Trabajo de grado'         => 'Trabajo de grado',
                                    'Investigación profesoral' => 'Investigación profesoral',
                                ])
                                ->columns(3)
                                ->required(),

                            Select::make('laboratory_id')
                                ->label('Espacio académico')
                                ->options(fn() => \App\Models\Laboratory::pluck('name', 'id'))
                                ->required(),

                            Select::make('academic_program')
                                ->label('Programa académico')
                                ->options([
                                    'Ingeniería de Sistemas'     => 'Ingeniería de Sistemas',
                                    'Ingeniería Industrial'      => 'Ingeniería Industrial',
                                    'Contaduría Pública'         => 'Contaduría Pública',
                                    'Administración de Empresas' => 'Administración de Empresas',
                                ])
                                ->required(),

                            Select::make('semester')
                                ->label('Semestre')
                                ->options(array_combine(range(1, 10), range(1, 10)))
                                ->required(),

                            TextInput::make('applicants')
                                ->label('Solicitantes')
                                ->required(),

                            TextInput::make('research_name')
                                ->label('Investigación')
                                ->required(),

                            TextInput::make('advisor')
                                ->label('Asesor')
                                ->required(),
                        ]),

                        Section::make('MATERIALES Y EQUIPOS')->schema([
                            Select::make('products')
                                ->label('Productos disponibles')
                                ->multiple()
                                ->searchable()
                                ->options(
                                    fn() => Product::with('laboratory')
                                        ->get()
                                        ->mapWithKeys(fn($p) => [
                                            $p->id => "{$p->name} — {$p->laboratory->name}",
                                        ])->toArray()
                                )
                                ->required(),
                        ]),

                        Section::make('Horario')->columns(3)->schema([
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
                    ])
                    ->action(function (Schedule $record, array $data): void {
                        Booking::create([
                            'schedule_id'      => $record->id,
                            'user_id'          => Auth::id(),
                            'project_type'     => $data['project_type'],
                            'laboratory_id'    => $data['laboratory_id'],
                            'academic_program' => $data['academic_program'],
                            'semester'         => $data['semester'],
                            'applicants'       => $data['applicants'],
                            'research_name'    => $data['research_name'],
                            'advisor'          => $data['advisor'],
                            'products'         => json_encode($data['products']),
                            'start_at'         => $data['start_at'],
                            'end_at'           => $data['end_at'],
                            'color'            => $data['color'],
                            'status'           => 'pending',
                        ]);

                        Action::message('¡Solicitud enviada!');
                    }),
            ])
            ->headerActions([
                Action::make('ver_calendario')
                    ->label('Ver Calendario')
                    ->icon('heroicon-o-calendar')
                    ->modalHeading('Calendario de Reservas')
                    ->modalWidth('4xl')
                    // Aquí la closure que retorna la vista
                    ->modalContent(fn() => view('filament.pages.booking-calendar')),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
        ];
    }
}
