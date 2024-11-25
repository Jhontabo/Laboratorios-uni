<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Horarios y reservas';
    protected static ?string $label = 'Horarios';
    protected static ?int $navigationSort = 2; // Orden en el menú de navegación

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dia_semana')
                    ->required()
                    ->label('Día de la semana')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo',
                    ])
                    ->placeholder('Selecciona un día'),

                Forms\Components\TimePicker::make('hora_inicio')
                    ->required()
                    ->label('Hora de inicio')
                    ->placeholder('Selecciona la hora'),

                Forms\Components\TimePicker::make('hora_fin')
                    ->required()
                    ->label('Hora de fin')
                    ->placeholder('Selecciona la hora')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('hora_inicio') && $state <= $get('hora_inicio')) {
                            $set('hora_fin', null);
                            throw new \Exception('La hora de fin debe ser mayor a la hora de inicio.');
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('dia_semana')
                    ->label('Día de la semana')
                    ->colors([
                        'primary' => 'Lunes',
                        'success' => 'Martes',
                        'info' => 'Miércoles',
                        'warning' => 'Jueves',
                        'danger' => 'Viernes',
                        'secondary' => ['Sábado', 'Domingo'],
                    ]),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora de inicio')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_fin')
                    ->label('Hora de fin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dia_semana')
                    ->label('Día de la semana')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHorarios::route('/'),
            'create' => Pages\CreateHorario::route('/create'),
            'edit' => Pages\EditHorario::route('/{record}/edit'),
        ];
    }
}
