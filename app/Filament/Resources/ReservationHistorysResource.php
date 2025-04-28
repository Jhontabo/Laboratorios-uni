<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationHistorysResource\Pages;
use App\Models\Booking;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReservationHistorysResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'My Reservations';
    protected static ?string $modelLabel = 'Reservation';
    protected static ?string $pluralModelLabel = 'Reservations';
    protected static ?string $navigationGroup = 'Academic Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        $pending = static::getModel()::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        return $pending > 0 ? 'warning' : 'success';
    }

    public static function query(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id())
            ->with(['schedule', 'laboratory'])
            ->latest();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('laboratory.name')
                    ->label('Laboratory')
                    ->description(fn($record) => $record->laboratory?->location ?? 'No location')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('interval')
                    ->label('Schedule')
                    ->getStateUsing(function ($record) {
                        if (!$record->schedule) {
                            return 'Not assigned';
                        }
                        $start = $record->schedule->start_at->format('d M Y, H:i');
                        $end = $record->schedule->end_at->format('H:i');
                        return "$start - $end";
                    })
                    ->description(fn($record) => $record->schedule?->description ?? 'No description')
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pending Approval',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        default => ucfirst($state),
                    })
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
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Reservation Status'),

                SelectFilter::make('laboratory')
                    ->relationship('laboratory', 'name')
                    ->label('Filter by Laboratory'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('View Details'),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No reservations yet')
            ->emptyStateDescription('Your reservations will appear here once you create them.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservationHistories::route('/'),
            'view' => Pages\ViewReservationHistory::route('/{record}'),
        ];
    }
}

