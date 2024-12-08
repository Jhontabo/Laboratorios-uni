<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HorarioResource\Pages;
use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Builder;
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
                TextColumn::make('title')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('laboratorio.nombre')->label('Laboratorio')->sortable(),
                TextColumn::make('laboratorio.laboratorista.name') // Usamos el accesorio "name" del modelo User
                    ->label('Encargado')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')->label('Creado en')->dateTime(),
                TextColumn::make('reservation_status')
                    ->label('Estado')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? 'Aceptada' : 'Rechazada') // Muestra "Aceptada" o "Rechazada"
                    ->badge() // Aplica estilos de badge
                    ->colors([
                        'primary' => fn($state) => $state, // Si es true, color "primary"
                        'danger' => fn($state) => !$state, // Si es false, color "danger"
                    ]),
            ])
            ->filters([
                SelectFilter::make('id_laboratorio') // Filtro por laboratorio
                    ->label('Laboratorio')
                    ->relationship('laboratorio', 'nombre')
                    ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
                    ->placeholder('Todos los laboratorios'),
            ])
            ->actions([
                Tables\Actions\Action::make('Confirmar')
                    ->action(function (Horario $record) {
                        $record->reservation_status = true;
                        $record->save();
                    })
                    ->visible(fn(Horario $record) => !$record->reservation_status)
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Tables\Actions\Action::make('Cancelar')
                    ->action(function (Horario $record) {
                        $record->reservation_status = false;
                        $record->save();
                    })
                    ->visible(fn(Horario $record) => $record->reservation_status)
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\Calendar::route('/'),
        ];
    }
}
