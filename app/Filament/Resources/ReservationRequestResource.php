<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationRequestResource\Pages;
use App\Models\Booking; // Ensure you're using the correct model
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ReservationRequestResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Solicitud reserva';
    protected static ?string $navigationGroup = 'Gestion de Reservas';
    protected static ?string $modelLabel = 'Horario';
    protected static ?string $pluralLabel = 'Solicitud de reservas';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        // Lógica personalizada
        return $user && $user->hasRole('ADMIN') || $user->hasRole('LABORATORISTA') || $user->hasRole('COORDINADOR');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('status', 'pending')->count() > 1 ? 'warning' : 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                TextColumn::make('laboratory.name') // Asegúrate de que 'schedule.laboratory.location' sea la ruta correcta
                    ->label('Laboratorio')
                    ->description(fn($record) => $record->laboratory?->location ?? 'No location') // Usa 'schedule.laboratory.location'
                    ->searchable()
                    ->icon('heroicon-o-building-office'),
                TextColumn::make('user.name') // Usamos 'user.name' para acceder al nombre completo
                    ->label('Aplicante')
                    ->formatStateUsing(fn($record) => "{$record->user->name} {$record->user->last_name}")
                    ->description(fn($record) => $record->user->email ?? 'No email')
                    ->icon('heroicon-o-user'),

                TextColumn::make('interval')
                    ->label('Intervalo')
                    ->getStateUsing(fn($record) => $record->schedule && $record->schedule->start_at && $record->schedule->end_at
                        ? $record->schedule->start_at->format('d M Y, H:i') . ' - ' . $record->schedule->end_at->format('H:i')
                        : 'Not assigned')
                    ->description(fn($record) => $record->schedule?->description ?? 'No description')
                    ->icon('heroicon-o-clock'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprovado',
                        'rejected' => 'Rechazado',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icon(fn($state) => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => null,
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha peticion')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
            ])

            ->actions([
                Action::make('Aprobar')
                    ->action(function (Booking $record) { // Ensure you're using the correct model Booking
                        $record->status = 'approved';
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Reservation Approved')
                            ->body("Reservation for {$record->user_first_name} has been approved.")
                            ->send();
                    })
                    ->visible(fn(Booking $record) => $record->status === 'pending')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Approve Reservation')
                    ->modalDescription('Are you sure you want to approve this reservation request?')
                    ->requiresConfirmation(),

                Action::make('Rechazar')
                    ->form([
                        Textarea::make('rejection_reason') // Changed 'razon_rechazo' to 'rejection_reason'
                            ->label('Reason for Rejection')
                            ->required()
                            ->placeholder('State the reason for rejecting this request')
                            ->maxLength(501),
                    ])
                    ->action(function (Booking $record, array $data) { // Using Booking instead of Reserva
                        $record->status = 'rejected';
                        $record->rejection_reason = $data['rejection_reason']; // Changed 'razon_rechazo' to 'rejection_reason'
                        $record->save();

                        Notification::make()
                            ->danger()
                            ->title('Reservation Rejected')
                            ->body("Reservation rejected. Reason: {$data['rejection_reason']}")
                            ->send();
                    })
                    ->visible(fn(Booking $record) => $record->status === 'pending')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->modalHeading('Reject Reservation')
                    ->modalDescription('Please specify the reason for rejecting the request.'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('Ver detalles'),
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
