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
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Título del evento')
                    ->placeholder('Ejemplo: Clase de Programación'),

                Forms\Components\ColorPicker::make('color')
                    ->required()
                    ->label('Color del evento'),

                // Asegúrate de que estos campos sean de tipo `datetime`
                Forms\Components\DateTimePicker::make('start_at')
                    ->required()
                    ->label('Fecha y hora de inicio')
                    ->withoutSeconds() // Elimina segundos si no son necesarios
                    ->placeholder('Selecciona la fecha y hora de inicio'),

                Forms\Components\DateTimePicker::make('end_at')
                    ->required()
                    ->label('Fecha y hora de fin')
                    ->withoutSeconds() // Elimina segundos si no son necesarios
                    ->placeholder('Selecciona la fecha y hora de fin')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('start_at') && $state <= $get('start_at')) {
                            $set('end_at', null);
                            throw new \Exception('La fecha y hora de fin deben ser posteriores a la de inicio.');
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título del evento')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('color')
                    ->label('Color del evento'),

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
