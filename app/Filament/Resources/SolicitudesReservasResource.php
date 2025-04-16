<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudesReservasResource\Pages;
use App\Models\Reserva;
use Filament\Forms\Components\Textarea;
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

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Solicitudes de Reservas';
    protected static ?string $modelLabel = 'Solicitud de Reserva';
    protected static ?string $pluralModelLabel = 'Solicitudes de Reservas';
    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'pendiente')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('estado', 'pendiente')->count() > 0
            ? 'warning'
            : 'success';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('ver panel solicitudes reservas') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('horario.laboratorio.nombre')
                    ->label('Laboratorio')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->horario?->laboratorio?->ubicacion ?? 'Sin ubicación')
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('usuario.name')
                    ->label('Solicitante')
                    ->formatStateUsing(fn($record) => $record->nombre_usuario . ' ' . $record->apellido_usuario)
                    ->description(fn($record) => $record->correo_usuario)
                    ->searchable(['nombre_usuario', 'apellido_usuario'])
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('intervalo')
                    ->label('Horario')
                    ->getStateUsing(
                        fn($record) => $record->horario && $record->horario->start_at && $record->horario->end_at
                            ? $record->horario->start_at->format('d M Y, H:i') . ' - ' . $record->horario->end_at->format('H:i')
                            : 'No asignado'
                    )
                    ->description(fn($record) => $record->horario?->descripcion ?? 'Sin descripción')
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pendiente de revisión',
                        'aceptada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                        default => 'Desconocido',
                    })
                    ->badge()
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'aceptada',
                        'danger' => 'rechazada',
                    ])
                    ->icon(fn($state) => match ($state) {
                        'pendiente' => 'heroicon-o-clock',
                        'aceptada' => 'heroicon-o-check-circle',
                        'rechazada' => 'heroicon-o-x-circle',
                        default => null,
                    }),

                TextColumn::make('created_at')
                    ->label('Solicitado')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendientes',
                        'aceptada' => 'Aprobadas',
                        'rechazada' => 'Rechazadas',
                    ])
                    ->label('Estado de Reserva'),

                Tables\Filters\SelectFilter::make('laboratorio')
                    ->relationship('horario.laboratorio', 'nombre')
                    ->label('Filtrar por Laboratorio'),
            ])
            ->actions([
                Action::make('Aprobar')
                    ->action(function (Reserva $record) {
                        $record->estado = 'aceptada';
                        $record->save();

                        Notification::make()
                            ->title('Reserva Aprobada')
                            ->success()
                            ->body("La reserva de {$record->nombre_usuario} ha sido aprobada.")
                            ->send();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Aprobar Reserva')
                    ->modalDescription('¿Está seguro que desea aprobar esta solicitud de reserva?')
                    ->requiresConfirmation(),

                Action::make('Rechazar')
                    ->form([
                        Textarea::make('razon_rechazo')
                            ->label('Motivo del Rechazo')
                            ->required()
                            ->placeholder('Indique el motivo por el cual rechaza esta solicitud')
                            ->maxLength(500),
                    ])
                    ->action(function (Reserva $record, array $data) {
                        $record->estado = 'rechazada';
                        $record->razon_rechazo = $data['razon_rechazo'];
                        $record->save();

                        Notification::make()
                            ->title('Reserva Rechazada')
                            ->danger()
                            ->body("Reserva rechazada. Motivo: {$data['razon_rechazo']}")
                            ->send();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->modalHeading('Rechazar Reserva')
                    ->modalDescription('Por favor indique el motivo del rechazo.'),

                Action::make('Detalles')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalContent(fn($record) => view('filament.resources.solicitudes-reservas.details', [
                        'record' => $record
                    ]))
                    ->modalWidth('4xl'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ])
            ->emptyStateHeading('No hay solicitudes de reserva')
            ->emptyStateDescription('Cuando los usuarios realicen reservas, aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolicitudesReservas::route('/'),
        ];
    }
}
