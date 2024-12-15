<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Horario;
use App\Models\Reserva;
use Filament\Notifications\Notification;
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
            ->query(fn() => Horario::with(['reservas' => fn($query) => $query->latest('created_at')]) // Traer solo la última reserva por horario
                ->where('is_available', true)) // Mostrar solo horarios disponibles
            ->columns([
                TextColumn::make('title') // Título del horario
                    ->label('Título del Horario')
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
                TextColumn::make('estado_actual') // Estado actual de la reserva
                    ->label('Estado de Reserva')
                    ->getStateUsing(fn($record) => $record->reservas->first()?->estado ?? 'En espera') // Muestra el estado o "En espera" si no hay reservas
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'aceptada',
                        'danger' => 'rechazada',
                        'secondary' => 'En espera', // Color para "En espera"
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
                        $user = Auth::user();

                        if (!$user) {
                            Notification::make()
                                ->title('Acción no permitida')
                                ->danger()
                                ->body('Debes iniciar sesión para realizar una reserva.')
                                ->send();
                            return;
                        }

                        // Verifica si ya existe una solicitud pendiente para este horario
                        $existingReservation = Reserva::where('id_horario', $record->id_horario)
                            ->where('id_usuario', $user->id_usuario)
                            ->where('estado', 'pendiente')
                            ->exists();

                        if ($existingReservation) {
                            Notification::make()
                                ->title('Reserva pendiente')
                                ->warning()
                                ->body('Ya tienes una solicitud pendiente para este horario.')
                                ->send();
                            return;
                        }

                        // Crear la reserva asociada
                        Reserva::create([
                            'id_horario' => $record->id_horario,
                            'id_usuario' => $user->id_usuario,
                            'nombre_usuario' => $user->nombre,
                            'apellido_usuario' => $user->apellido,
                            'correo_usuario' => $user->email,
                            'estado' => 'pendiente',
                        ]);

                        Notification::make()
                            ->title('Reserva creada')
                            ->success()
                            ->body('Tu solicitud de reserva ha sido enviada correctamente.')
                            ->send();
                    })
                    ->visible(fn(Horario $record) => $record->is_available) // Mostrar solo si está disponible
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
