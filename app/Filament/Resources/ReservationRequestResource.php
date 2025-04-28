<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationRequestResource\Pages;
use App\Models\Reserva;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ReservationRequestResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Reservation Requests';
    protected static ?string $modelLabel = 'Reservation Request';
    protected static ?string $pluralModelLabel = 'Reservation Requests';
    protected static ?string $navigationGroup = 'Academic Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'pendiente')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('estado', 'pendiente')->count() > 0 ? 'warning' : 'success';
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
                    ->label('Laboratory')
                    ->description(fn($record) => $record->horario?->laboratorio?->ubicacion ?? 'No location')
                    ->searchable()
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('usuario.name')
                    ->label('Applicant')
                    ->formatStateUsing(fn($record) => "{$record->nombre_usuario} {$record->apellido_usuario}")
                    ->description(fn($record) => $record->correo_usuario)
                    ->searchable(['nombre_usuario', 'apellido_usuario'])
                    ->icon('heroicon-o-user'),

                TextColumn::make('intervalo')
                    ->label('Schedule')
                    ->getStateUsing(fn($record) => $record->horario && $record->horario->start_at && $record->horario->end_at
                        ? $record->horario->start_at->format('d M Y, H:i') . ' - ' . $record->horario->end_at->format('H:i')
                        : 'Not assigned')
                    ->description(fn($record) => $record->horario?->descripcion ?? 'No description')
                    ->icon('heroicon-o-clock'),

                TextColumn::make('estado')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pending Review',
                        'aceptada' => 'Approved',
                        'rechazada' => 'Rejected',
                        default => ucfirst($state),
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
                    ->label('Requested')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pending',
                        'aceptada' => 'Approved',
                        'rechazada' => 'Rejected',
                    ])
                    ->label('Reservation Status'),

                Tables\Filters\SelectFilter::make('laboratorio')
                    ->relationship('horario.laboratorio', 'nombre')
                    ->label('Filter by Laboratory'),
            ])
            ->actions([
                Action::make('Approve')
                    ->action(function (Reserva $record) {
                        $record->estado = 'aceptada';
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Reservation Approved')
                            ->body("Reservation for {$record->nombre_usuario} has been approved.")
                            ->send();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Approve Reservation')
                    ->modalDescription('Are you sure you want to approve this reservation request?')
                    ->requiresConfirmation(),

                Action::make('Reject')
                    ->form([
                        Textarea::make('razon_rechazo')
                            ->label('Reason for Rejection')
                            ->required()
                            ->placeholder('State the reason for rejecting this request')
                            ->maxLength(500),
                    ])
                    ->action(function (Reserva $record, array $data) {
                        $record->estado = 'rechazada';
                        $record->razon_rechazo = $data['razon_rechazo'];
                        $record->save();

                        Notification::make()
                            ->danger()
                            ->title('Reservation Rejected')
                            ->body("Reservation rejected. Reason: {$data['razon_rechazo']}")
                            ->send();
                    })
                    ->visible(fn(Reserva $record) => $record->estado === 'pendiente')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->modalHeading('Reject Reservation')
                    ->modalDescription('Please specify the reason for rejecting the request.'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('View Details'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ])
            ->emptyStateHeading('No reservation requests')
            ->emptyStateDescription('Reservation requests from users will appear here.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservationRequests::route('/'),
            'view' => Pages\ViewReservationRequest::route('/{record}'),
        ];
    }
}

