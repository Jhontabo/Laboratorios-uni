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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('dia_semana')
                    ->required()
                    ->label('Día de la semana'),

                Forms\Components\TimePicker::make('hora_inicio')
                    ->required()
                    ->label('Hora de inicio'),

                Forms\Components\TimePicker::make('hora_fin')
                    ->required()
                    ->label('Hora de fin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dia_semana')
                    ->label('Día de la semana'),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora de inicio'),

                Tables\Columns\TextColumn::make('hora_fin')
                    ->label('Hora de fin'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime(),
            ])
            ->filters([
                //
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
