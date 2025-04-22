<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoDisponibleResource\Pages;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Prestamo;
use App\Models\ProductoDisponible;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\Stack;

class ProductoDisponibleResource extends Resource
{
    protected static ?string $model = ProductoDisponible::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('disponible_para_prestamo', true);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->size(50)
                    ->circular()
                    ->toggleable(),

                TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => substr($record->descripcion, 0, 50) . '...')
                    ->weight('medium')
                    ->color('primary'),

                TextColumn::make('cantidad_disponible')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($record) => $record->cantidad_disponible > 10 ? 'success' : ($record->cantidad_disponible > 0 ? 'warning' : 'danger'))
                    ->icon(fn($record) => $record->cantidad_disponible > 10 ? 'heroicon-o-check-circle' : ($record->cantidad_disponible > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-x-circle')),

                TextColumn::make('estado_producto')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'nuevo' => 'success',
                        'usado' => 'warning',
                        'dañado' => 'danger',
                        'dado_de_baja' => 'gray',
                        'perdido' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'nuevo' => 'Nuevo',
                        'usado' => 'Usado',
                        'dañado' => 'Dañado',
                        'dado_de_baja' => 'Baja',
                        'perdido' => 'Perdido',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('tipo_producto')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'equipo' => 'info',
                        'suministro' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'equipo' => 'Equipo',
                        'suministro' => 'Suministro',
                        default => $state,
                    })
                    ->sortable(),




                TextColumn::make('laboratorio.ubicacion')
                    ->label('Ubicación')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('categoria.nombre_categoria')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye'),
            ])

            ->headerActions([
                // Acción para mostrar el modal con las instrucciones de cómo solicitar productos
                Tables\Actions\Action::make('infoSeleccion')
                    ->label('¿Cómo solicitar productos?')
                    ->color('gray')
                    ->icon('heroicon-o-question-mark-circle')
                    // Esto abre un modal con el contenido de instrucciones
                    ->modalContent(view('filament.pages.instrucciones-pedido'))
                    ->modalSubmitAction(false)  // Desactivar acción de envío
                    ->modalCancelActionLabel('Entendido')  // Etiqueta para el botón de cancelar
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('solicitarPrestamo')
                    ->label('Solicitar productos (máx. 5)')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->action(function ($records) {
                        if ($records->count() > 5) {
                            Notification::make()
                                ->title('Límite excedido')
                                ->body('Solo puedes seleccionar hasta 5 productos')
                                ->danger()
                                ->send();
                            return;
                        }

                        foreach ($records as $record) {
                            if ($record->cantidad_disponible <= 0) {
                                Notification::make()
                                    ->title("{$record->nombre} no disponible")
                                    ->danger()
                                    ->send();
                                continue;
                            }

                            // Crear registro en préstamos
                            Prestamo::create([
                                'id_producto' => $record->id_producto,
                                'user_id' => auth()->id(),
                                'estado' => 'pendiente',
                                'fecha_solicitud' => now(),

                            ]);


                        }

                        Notification::make()
                            ->title('Solicitud registrada')
                            ->body("{$records->count()} productos solicitados correctamente")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar solicitud')
                    ->modalDescription(fn($records) => "¿Confirmas la solicitud de {$records->count()} productos?")
                    ->deselectRecordsAfterCompletion(),
            ])
            ->emptyState(view('filament.pages.empty-state-productos'))
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductoDisponibles::route('/'),
            'view' => Pages\ViewProductoDisponible::route('/{record}'),
        ];
    }
}
