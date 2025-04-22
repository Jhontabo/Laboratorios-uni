<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GestionPrestamoResource\Pages;
use App\Models\ProductoDisponible;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GestionPrestamoResource extends Resource
{
    protected static ?string $model = ProductoDisponible::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Gestión de Préstamos';
    protected static ?string $modelLabel = 'Gestion de préstamos';
    protected static ?string $navigationGroup = 'Préstamos';

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()
            ->whereIn('estado_prestamo', ['pendiente', 'aprobado', 'devuelto'])
            ->whereNotNull('user_id');
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cantidad_disponible')
                    ->label('Stock')
                    ->sortable()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),


                Tables\Columns\ColumnGroup::make('Usuario')
                    ->columns([
                        TextColumn::make('usuario.name')
                            ->label('Nombre')
                            ->formatStateUsing(fn($state, $record) => "{$record->usuario->name} {$record->usuario->apellido}")
                            ->searchable(query: function (Builder $query, string $search): Builder {
                                return $query->whereHas('usuario', function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('apellido', 'like', "%{$search}%");
                                });
                            })
                        ,

                        TextColumn::make('usuario.email')
                            ->label('Correo')
                            ->searchable()


                    ]),

                TextColumn::make('estado_prestamo')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado' => 'success',
                        'rechazado' => 'danger',
                        'devuelto' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'devuelto' => 'Devuelto',
                        default => $state,
                    }),

                TextColumn::make('fecha_solicitud')
                    ->label('Solicitud')
                    ->dateTime('d M Y')
                    ->sortable(),


                TextColumn::make('fecha_aprobacion')
                    ->label('Aprobación')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('No aprobado')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('fecha_devolucion_estimada')
                    ->label('Devolución Estimada')
                    ->dateTime('d M Y')
                    ->color(
                        fn($record) =>
                        $record->estado_prestamo === 'aprobado' &&
                        $record->fecha_devolucion_estimada < now()
                        ? 'danger'
                        : 'success'
                    )
                    ->sortable()
                    ->placeholder('No asignada')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('fecha_devolucion_real')->label('Devuelto')->dateTime('d M Y H:i')->sortable()->placeholder('No devuelto'),
            ])
            ->actions([
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_devolucion_estimada')
                            ->label('Fecha estimada de devolución')
                            ->required()
                            ->minDate(now()->addDay())
                            ->default(now()->addWeek())
                            ->displayFormat('d M Y')
                    ])
                    ->action(function (ProductoDisponible $record, array $data) {
                        $fechaDevolucion = \Carbon\Carbon::parse($data['fecha_devolucion_estimada']);
                        $record->update([
                            'estado_prestamo' => 'aprobado',
                            'fecha_aprobacion' => now(),
                            'fecha_devolucion_estimada' => $fechaDevolucion,
                            'disponible_para_prestamo' => true
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Préstamo aprobado')
                            ->body("Fecha límite: " . $fechaDevolucion->format('d/m/Y'))
                            ->send();
                    })
                    ->visible(fn($record) => $record->estado_prestamo === 'pendiente'),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar préstamo')
                    ->modalDescription('¿Estás seguro de rechazar este préstamo?')
                    ->action(function (ProductoDisponible $record) {
                        $record->update([
                            'estado_prestamo' => 'rechazado',
                            'disponible_para_prestamo' => true,
                            'fecha_devolucion_estimada' => null
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Préstamo rechazado')
                            ->send();
                    })
                    ->visible(fn($record) => $record->estado_prestamo === 'pendiente'),

                Tables\Actions\Action::make('devolver')
                    ->label('Marcar como devuelto')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DatePicker::make('fecha_devolucion_real')
                            ->label('Fecha real de devolución')
                            ->default(now())
                            ->maxDate(now())
                            ->displayFormat('d M Y')
                    ])
                    ->action(function (ProductoDisponible $record, array $data) {
                        $fechaDevolucionReal = \Carbon\Carbon::parse($data['fecha_devolucion_real']);
                        $record->update([
                            'estado_prestamo' => 'devuelto',
                            'disponible_para_prestamo' => true,
                            'fecha_devolucion_real' => $fechaDevolucionReal
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Equipo devuelto')
                            ->body("Fecha de devolución: {$fechaDevolucionReal->format('d/m/Y')}")
                            ->send();
                    })
                    ->visible(fn($record) => $record->estado_prestamo === 'aprobado'),
            ])
            ->filters([
                SelectFilter::make('estado_prestamo')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendientes',
                        'aprobado' => 'Aprobados',
                        'rechazado' => 'Rechazados',
                        'devuelto' => 'Devueltos',
                    ]),

                Tables\Filters\Filter::make('prestamos_vencidos')
                    ->label('Préstamos vencidos')
                    ->query(
                        fn(Builder $query): Builder => $query
                            ->where('estado_prestamo', 'aprobado')
                            ->where('fecha_devolucion_estimada', '<', now())
                    ),

                Tables\Filters\Filter::make('prestamos_por_vencer')
                    ->label('Por vencer (3 días)')
                    ->query(
                        fn(Builder $query): Builder => $query
                            ->where('estado_prestamo', 'aprobado')
                            ->whereBetween('fecha_devolucion_estimada', [now(), now()->addDays(3)])
                    ),
            ])

            ->bulkActions([

                Tables\Actions\BulkAction::make('aprobar')
                    ->label('Aprobar seleccionados')
                    ->icon('heroicon-o-check')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_devolucion_estimada')
                            ->label('Fecha estimada de devolución')
                            ->required()
                            ->minDate(now()->addDay())
                            ->default(now()->addWeek())
                    ])
                    ->action(function ($records, array $data) {
                        $fechaDevolucion = \Carbon\Carbon::parse($data['fecha_devolucion_estimada']);
                        $records->each->update([
                            'estado_prestamo' => 'aprobado',
                            'fecha_aprobacion' => now(),
                            'fecha_devolucion_estimada' => $fechaDevolucion,
                            'disponible_para_prestamo' => false
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Préstamos aprobados')
                            ->body("Fecha límite: {$fechaDevolucion->format('d/m/Y')}")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),


            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionPrestamos::route('/'),
        ];
    }
}