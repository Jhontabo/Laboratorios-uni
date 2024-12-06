<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use App\Models\Laboratorio;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Horarios y reservas';
    protected static ?string $label = 'Horarios';
    protected static ?int $navigationSort = 2; // Orden en el menú de navegación

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn() => Horario::where('is_available', true)) // Filtra horarios disponibles
            ->columns([
                TextColumn::make('id_horario')->label('ID')->sortable(),
                TextColumn::make('title')->label('Título')->sortable()->searchable(),
                TextColumn::make('description')->label('Descripción'),
                TextColumn::make('laboratorio.nombre')->label('Laboratorio')->sortable(),
                TextColumn::make('created_at')->label('Creado en')->dateTime(),
                TextColumn::make('updated_at')->label('Actualizado en')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('id_laboratorio') // Usa el ID para filtrar
                    ->label('Laboratorio')
                    ->relationship('laboratorio', 'nombre') // Relación con el modelo Laboratorio
                    ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
                    ->placeholder('Todos los laboratorios'),
            ])
            ->actions([])
            ->bulkActions([]);
    }





    public static function getPages(): array
    {
        return [
            'index' => Pages\Calendar::route('/'),
        ];
    }
}
