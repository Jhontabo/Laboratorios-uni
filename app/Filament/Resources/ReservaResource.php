<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Horario;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ReservaResource extends Resource
{
    protected static ?string $model = Horario::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn() => Horario::with(['laboratorio', 'laboratorio.laboratorista' => fn($query) => $query->whereHas('roles', fn($roleQuery) => $roleQuery->where('name', 'LABORATORISTA'))])
                ->where('is_available', true)) // Mostrar solo horarios disponibles
            ->columns([
                TextColumn::make('title') // Título del horario
                    ->label('Título del Horario')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('laboratorio.nombre') // Nombre del laboratorio
                    ->label('Laboratorio')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('laboratorio.laboratorista.nombre') // Nombre del laboratorista
                    ->label('Encargado del Laboratorio')
                    ->formatStateUsing(fn($record) => $record?->laboratorio?->laboratorista?->nombre ?? 'N/A') // Fallback si no hay laboratorista
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_at') // Fecha y hora de inicio
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_at') // Fecha y hora de fin
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estado') // Estado de la reserva
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aceptada' => 'Aceptada',
                        'rechazada' => 'Rechazada',
                        default => 'Disponible',
                    })
                    ->badge()
                    ->colors([
                        'primary' => 'pendiente',
                        'success' => 'aceptada',
                        'danger' => 'rechazada',
                    ]),
            ])
            ->filters([
                SelectFilter::make('id_laboratorio') // Filtro por laboratorio
                    ->label('Laboratorio')
                    ->relationship('laboratorio', 'nombre')
                    ->options(fn() => Horario::with('laboratorio')
                        ->get()
                        ->pluck('laboratorio.nombre', 'laboratorio.id')),
            ])
            ->actions([
                Action::make('Reservar')
                    ->action(function (Horario $record) {
                        // Obtener el usuario autenticado
                        $user = Auth::user();

                        if (!$user) {
                            throw new \Exception('Usuario no autenticado.');
                        }

                        // Verificar si ya existe una solicitud pendiente para este usuario
                        $existingReservation = Reserva::where('id_horario', $record->id_horario)
                            ->where('id_usuario', $user->id_usuario)
                            ->where('estado', 'pendiente')
                            ->exists();

                        if ($existingReservation) {
                            throw new \Exception('Ya tienes una solicitud pendiente para este horario.');
                        }

                        // Crear la reserva
                        Reserva::create([
                            'id_horario' => $record->id_horario,
                            'id_usuario' => $user->id_usuario,
                            'nombre_usuario' => $user->nombre,
                            'apellido_usuario' => $user->apellido,
                            'correo_usuario' => $user->correo_electronico,
                            'estado' => 'pendiente',
                        ]);
                    })
                    ->visible(fn(Horario $record) => $record->is_available) // Mostrar solo si el horario está disponible
                    ->color('primary')
                    ->icon('heroicon-o-plus'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservas::route('/'),
        ];
    }
}
