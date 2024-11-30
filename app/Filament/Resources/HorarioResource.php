<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
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
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Motivo de la reserva')
                    ->maxLength(255)
                    ->placeholder('Clase de Programación'),

                Forms\Components\DateTimePicker::make('start_at')
                    ->required()
                    ->label('Fecha y hora de inicio')
                    ->withoutSeconds()
                    ->placeholder('Selecciona la fecha y hora de inicio')
                    ->helperText('No se puede seleccionar una fecha pasada')
                    // Validación de fecha posterior a la actual
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state && $state < now()) {
                            // Añadir mensaje de error visual
                            return 'La fecha y hora de inicio no pueden ser anteriores a la fecha actual.';
                        }
                    }),

                Forms\Components\DateTimePicker::make('end_at')
                    ->required()
                    ->label('Fecha y hora de fin')
                    ->withoutSeconds()
                    ->placeholder('Selecciona la fecha y hora de fin')
                    ->helperText('No se puede seleccionar una fecha pasada')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // Validación de que la fecha de fin sea posterior a la de inicio
                        if ($state && $state < $get('start_at')) {
                            $set('end_at', null); // Limpiar el campo de fecha de fin
                            return 'La fecha y hora de fin deben ser posteriores a la de inicio.';
                        }

                        // Validación de fecha posterior a la actual para la fecha de fin
                        if ($state && $state < now()) {
                            return 'La fecha y hora de fin no pueden ser anteriores a la fecha actual.';
                        }
                    }),

                Textarea::make('description')
                    ->label('Descripción del evento')
                    ->maxLength(500)
                    ->placeholder('Ejemplo: Necesito computador...'),
            ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Motivo de la reserva')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Fecha y hora de inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_at')
                    ->label('Fecha y hora de fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
