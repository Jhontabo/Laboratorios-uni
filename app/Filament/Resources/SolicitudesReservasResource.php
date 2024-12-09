<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudesReservasResource\Pages;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class SolicitudesReservasResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Solicitudes de Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('horario.title') // Título del horario relacionado
                    ->label('Horario')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre_usuario') // Nombre del usuario que hizo la reserva
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('apellido_usuario') // Apellido del usuario
                    ->label('Apellido')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('correo_usuario') // Correo del usuario
                    ->label('Correo Electrónico')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('estado') // Estado de la reserva
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aceptada' => 'Aceptada',
                        'rechazada' => 'Rechazada',
                        default => 'Desconocido',
                    })
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'aceptada',
                        'danger' => 'rechazada',
                    ]),
                TextColumn::make('created_at') // Fecha de creación
                    ->label('Fecha de Creación')
                    ->dateTime(),
            ])
            ->actions([
                Action::make('Aceptar')
                    ->action(function (Reserva $record) { // Usa el modelo correcto
                        $record->estado = 'aceptada';
                        $record->save();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Action::make('Rechazar')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('razon_rechazo')
                            ->label('Razón del Rechazo')
                            ->required(),
                    ])
                    ->action(function (Reserva $record, array $data) {
                        $record->estado = 'rechazada';
                        $record->razon_rechazo = $data['razon_rechazo'];
                        $record->save();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
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
            'index' => Pages\ListSolicitudesReservas::route('/'),
        ];
    }
}
