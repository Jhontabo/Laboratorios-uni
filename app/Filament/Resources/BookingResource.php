<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Resources\BookingResource\Pages\ViewCalendar;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\{
    Section,
    Radio,
    Select,
    TextInput,
    DateTimePicker
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
                TableAction::make('reservar')
                    ->label('Reservar')
                    ->button()
                    ->modalHeading('Solicitud de Reserva')
                    ->modalWidth('lg')
                    ->form([
                        // Ya no preguntamos datos personales: los tomamos de Auth::user()
                        Section::make('Detalles de la práctica')->schema([
                            Radio::make('project_type')
                                ->label('Tipo de proyecto')
                                ->options([
                                    'Trabajo de grado'         => 'Trabajo de grado',
                                    'Investigación profesoral' => 'Investigación profesoral',
                                ])
                                ->columns(2)
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
                        Section::make('Materiales y equipos')->schema([
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
                        Section::make('Horario solicitado')->schema([
                            DateTimePicker::make('start_at')
                                ->label('Inicio')
                                ->required()
                                ->default(fn(Schedule $record) => $record->start_at),
                            DateTimePicker::make('end_at')
                                ->label('Fin')
                                ->required()
                                ->after('start_at')
                                ->default(fn(Schedule $record) => $record->end_at),
                        ]),
                    ])
                    ->action(function (Schedule $record, array $data, TableAction $action): void {
                        $user = Auth::user();

                        Booking::create([
                            'schedule_id'      => $record->id,
                            'user_id'          => $user->id,
                            'first_name'       => $user->first_name ?? $user->name,
                            'last_name'        => $user->last_name  ?? null,
                            'email'            => $user->email,
                            'project_type'     => $data['project_type'],
                            'laboratory_id'    => $data['laboratory_id'],
                            'academic_program' => $data['academic_program'],
                            'semester'         => $data['semester'],
                            'applicants'       => $data['applicants'],
                            'research_name'    => $data['research_name'],
                            'advisor'          => $data['advisor'],
                            'products'         => $data['products'], // se castea a array automáticamente
                            'start_at'         => $data['start_at'],
                            'end_at'           => $data['end_at'],
                            'status'           => Booking::STATUS_PENDING,
                        ]);

                        $action->success('¡Solicitud enviada y pendiente de aprobación!');
                    }),

                ViewAction::make('ver_calendario')
                    ->label('Ver Calendario')
                    ->icon('heroicon-o-calendar')
                    ->url(static::getUrl('calendar'))
                    ->openUrlInNewTab(),
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
            'index'    => ListBookings::route('/'),
            'calendar' => ViewCalendar::route('/calendar'),
        ];
    }
}
