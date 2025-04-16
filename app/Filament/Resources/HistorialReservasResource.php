<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialReservasResource\Pages;
use App\Models\Reserva;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HistorialReservasResource extends Resource
{
    protected static ?string $model = Reserva::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Mis Reservas';
    protected static ?string $modelLabel = 'Reserva';
    protected static ?string $pluralModelLabel = 'Mis Reservas';
    protected static ?string $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('id_usuario', Auth::id())->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        $pendientes = static::getModel()::where('id_usuario', Auth::id())
            ->where('estado', 'pendiente')
            ->count();

        return $pendientes > 0 ? 'warning' : 'success';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('ver panel historial reservas') ?? false;
    }

    public static function query(Builder $query): Builder
    {
        return $query->where('id_usuario', Auth::id())
            ->with(['horario', 'laboratorio'])
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
                TextColumn::make('laboratorio.nombre')
                    ->label('Laboratorio')
                    ->description(fn($record) => $record->laboratorio?->ubicacion ?? 'Sin ubicación')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office'),

                TextColumn::make('intervalo')
                    ->label('Horario')
                    ->getStateUsing(function ($record) {
                        if (!$record->horario) return 'No asignado';

                        $start = $record->horario->start_at->format('d M Y, H:i');
                        $end = $record->horario->end_at->format('H:i');
                        return "$start - $end";
                    })
                    ->description(fn($record) => $record->horario?->descripcion ?? 'Sin descripción')
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pendiente de aprobación',
                        'aceptada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                        default => $state,
                    })
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
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Solicitado')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendientes',
                        'aceptada' => 'Aprobadas',
                        'rechazada' => 'Rechazadas',
                    ])
                    ->label('Estado'),

                SelectFilter::make('laboratorio')
                    ->relationship('laboratorio', 'nombre')
                    ->label('Filtrar por Laboratorio'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('Ver Detalles'),
            ])

            ->bulkActions([])
            ->emptyStateHeading('No tienes reservas registradas')
            ->emptyStateDescription('Tus reservas aparecerán aquí cuando las realices')
            ->emptyStateIcon('heroicon-o-calendar')
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialReservas::route('/'),
            'view' => Pages\ViewHistorialReserva::route('/{record}'),
        ];
    }
}
