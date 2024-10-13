<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction; // Importar correctamente las acciones
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;

class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Configuración del formulario
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('dia_semana')
                    ->label('Día de la semana')
                    ->options([
                        'lunes' => 'Lunes',
                        'martes' => 'Martes',
                        'miércoles' => 'Miércoles',
                        'jueves' => 'Jueves',
                        'viernes' => 'Viernes',
                        'sábado' => 'Sábado',
                        'domingo' => 'Domingo',
                    ])
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->label('Hora de inicio')
                    ->required(),
                TimePicker::make('hora_fin')
                    ->label('Hora de fin')
                    ->required(),
            ]);
    }

    // Configuración de la tabla
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dia_semana')
                    ->label('Día de la semana'),
                TextColumn::make('hora_inicio')
                    ->label('Hora de inicio')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('H:i')), // Formato de hora
                TextColumn::make('hora_fin')
                    ->label('Hora de fin')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('H:i')), // Formato de hora
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(), // Acción de editar correctamente importada
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // Acción de eliminación masiva
                ]),
            ]);
    }

    // Definir las páginas del recurso
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHorarios::route('/'),
            'create' => Pages\CreateHorario::route('/create'),
            'edit' => Pages\EditHorario::route('/{record}/edit'),
        ];
    }
}
