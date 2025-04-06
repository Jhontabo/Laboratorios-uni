<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudesReservasResource\Pages;
use App\Models\Reserva;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SolicitudesReservasResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Solicitudes de Reservas';
    protected static ?string $navigationGroup = 'Horarios y reservas';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('ver panel solicitudes reservas') ?? false;
    }

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
                TextColumn::make('intervalo') // Intervalo de tiempo desde la relación
                    ->label('Intervalo de Tiempo')
                    ->getStateUsing(
                        fn($record) => $record->horario && $record->horario->start_at && $record->horario->end_at
                            ? $record->horario->start_at->format('d/m/Y H:i') . ' - ' . $record->horario->end_at->format('H:i')
                            : 'No asignado'
                    )
                    ->sortable(),
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
            ])
            ->actions([
                Action::make('Aceptar')
                    ->action(function (Reserva $record) {
                        $record->estado = 'aceptada';
                        $record->save();

                        Notification::make()
                            ->title('Reserva Aceptada')
                            ->success()
                            ->body('La reserva ha sido aceptada correctamente.')
                            ->send();
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

                        Notification::make()
                            ->title('Reserva Rechazada')
                            ->danger()
                            ->body('La reserva ha sido rechazada. Razón: ' . $data['razon_rechazo'])
                            ->send();
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
