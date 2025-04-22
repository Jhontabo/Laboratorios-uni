<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GestionPrestamoResource\Pages;
use App\Models\Prestamo;
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
use Illuminate\Support\Facades\DB;

class GestionPrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Gestión de Préstamos';
    protected static ?string $modelLabel = 'Gestion de préstamos';
    protected static ?string $navigationGroup = 'Préstamos';

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()
            ->with(['producto', 'usuario']) // Carga las relaciones anticipadamente
            ->whereIn('estado', ['pendiente', 'aprobado', 'devuelto'])
            ->whereNotNull('user_id');
    }

    public static function table(Table $table): Table
    {

        return $table
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns)
            ->columns([
                ImageColumn::make('producto.imagen')
                    ->label('Imagen')
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('producto.nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('producto.cantidad_disponible')
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

                TextColumn::make('estado')
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
                        $record->estado === 'aprobado' &&
                        $record->fecha_devolucion_estimada < now()
                        ? 'danger'
                        : 'success'
                    )
                    ->sortable()
                    ->placeholder('No asignada')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('fecha_devolucion_real')->label('Devuelto')->dateTime('d M Y')->sortable()->placeholder('No devuelto'),
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
                    ->action(function (Prestamo $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $producto = $record->producto;
                            $fechaDevolucionEstimada = \Carbon\Carbon::parse($data['fecha_devolucion_estimada']);

                            // Validar stock
                            if ($producto->cantidad_disponible <= 0) {
                                throw new \Exception('No hay unidades disponibles');
                            }

                            // Actualizar préstamo
                            $record->update([
                                'estado' => 'aprobado',
                                'fecha_aprobacion' => now(),
                                'fecha_devolucion_estimada' => $fechaDevolucionEstimada
                            ]);

                            // Actualizar producto
                            $producto->decrement('cantidad_disponible');
                            $producto->update([
                                'disponible_para_prestamo' => $producto->cantidad_disponible >= 5
                            ]);

                            // Notificación
                            $message = $producto->cantidad_disponible < 5
                                ? "Producto marcado como no disponible. Stock actual: {$producto->cantidad_disponible}"
                                : "Fecha límite: " . $fechaDevolucionEstimada->format('d/m/Y');

                            Notification::make()
                                ->success()
                                ->title('Préstamo aprobado')
                                ->body($message)
                                ->send();
                        });
                    })
                    ->visible(fn(Prestamo $record) => $record->estado === 'pendiente'),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar préstamo')
                    ->modalDescription('¿Estás seguro de rechazar este préstamo?')
                    ->action(function (Prestamo $record) {
                        $record->update([
                            'estado' => 'rechazado',
                            'fecha_devolucion_estimada' => null
                        ]);

                        // No modificamos el producto al rechazar
                        Notification::make()
                            ->danger()
                            ->title('Préstamo rechazado')
                            ->send();
                    })
                    ->visible(fn(Prestamo $record) => $record->estado === 'pendiente'),

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
                    ->action(function (Prestamo $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $fechaDevolucionReal = \Carbon\Carbon::parse($data['fecha_devolucion_real']);
                            $producto = $record->producto;

                            // Actualizar préstamo
                            $record->update([
                                'estado' => 'devuelto',
                                'fecha_devolucion_real' => $fechaDevolucionReal
                            ]);

                            // Actualizar producto
                            $producto->increment('cantidad_disponible');
                            $producto->update([
                                'disponible_para_prestamo' => true
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Equipo devuelto')
                                ->body("Fecha de devolución: {$fechaDevolucionReal->format('d/m/Y')}")
                                ->send();
                        });
                    })
                    ->visible(fn(Prestamo $record) => $record->estado === 'aprobado'),
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