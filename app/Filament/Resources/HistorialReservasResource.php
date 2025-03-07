<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialReservasResource\Pages;
use App\Models\Reserva;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HistorialReservasResource extends Resource
{
    protected static ?string $model = Reserva::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Historial de Reservas';
    protected static ?string $pluralLabel = 'Historial de Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';
    
    public static function query(Builder $query): Builder
    {
        return $query->where('id_usuario', Auth::id());
    }

    public static function canCreate(): bool
    {
        return false; // ❌ Oculta el botón "Crear reserva"
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_reserva')->label('ID')->sortable(),
                TextColumn::make('nombre_usuario')->label('Nombre'),
                TextColumn::make('apellido_usuario')->label('Apellido'),
                TextColumn::make('correo_usuario')->label('Correo'),
                TextColumn::make('horario.start_at')->label('Inicio')->dateTime(),
                TextColumn::make('horario.end_at')->label('Fin')->dateTime(),
                TextColumn::make('laboratorio.nombre')->label('Laboratorio'),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'pendiente' => 'warning',
                        'aceptada' => 'success',
                        'rechazada' => 'danger',
                    ])
                    ->sortable(),
                
                TextColumn::make('created_at')->label('Fecha de Reserva')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aceptada' => 'Aceptada',
                        'rechazada' => 'Rechazada',
                    ]),
            ])
            ->actions([]) // ❌ Sin acciones de edición o eliminación
            ->bulkActions([]); // ❌ Sin acciones masivas
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialReservas::route('/'),
        ];
    }
}