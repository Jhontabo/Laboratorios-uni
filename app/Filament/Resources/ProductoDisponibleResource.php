<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoDisponibleResource\Pages;
use App\Models\Categoria;
use App\Models\Laboratorio;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Esquema del formulario con todos los campos
            Select::make('id_laboratorio')
                ->label('Laboratorio')
                ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio'))
                ->required(),

            Select::make('id_categorias')
                ->label('Categoría')
                ->options(Categoria::all()->pluck('nombre_categoria', 'id'))
                ->required(),

            TextInput::make('nombre')
                ->label('Nombre del Producto')
                ->required(),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->required(),

            TextInput::make('numero_serie')
                ->label('Número de Serie')
                ->required(),

            TextInput::make('costo_unitario')
                ->label('Costo Unitario')
                ->numeric()
                ->required(),

            TextInput::make('cantidad_disponible')
                ->label('Cantidad Disponible')
                ->numeric()
                ->required(),

            DatePicker::make('fecha_adquisicion')
                ->label('Fecha de Adquisición')
                ->required(),

            Select::make('estado_producto')
                ->label('Estado del Producto')
                ->options([
                    'nuevo' => 'Nuevo',
                    'usado' => 'Usado',
                    'dañado' => 'Dañado',
                    'dado_de_baja' => 'Dado de Baja',
                    'perdido' => 'Perdido',
                ])
                ->required(),

            Select::make('tipo_producto')
                ->label('Tipo de Producto')
                ->options([
                    'equipo' => 'Equipo',
                    'suministro' => 'Suministro',
                ])
                ->required(),

            TextInput::make('imagen')
                ->label('Imagen del Producto')
                ->url()
                ->required(),

            Select::make('disponible_para_prestamo')
                ->label('Disponible para Préstamo')
                ->options([true => 'Sí', false => 'No'])
                ->required(),
        ]);
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

                TextColumn::make('costo_unitario')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('fecha_adquisicion')
                    ->label('Adquisición')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->filters([
                Filter::make('stock_bajo')
                    ->label('Stock bajo (<=10)')
                    ->query(fn(Builder $query) => $query->where('cantidad_disponible', '<=', 10))
                    ->toggle(),

                Filter::make('laboratorio')
                    ->label('Laboratorio')
                    ->form([
                        Select::make('id_laboratorio')
                            ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio'))
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data) =>
                        $query->when($data['id_laboratorio'], fn($q) => $q->where('id_laboratorio', $data['id_laboratorio']))
                    ),
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
                    ->label('Solicitar productos')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar solicitud de préstamo')
                    ->modalDescription('¿Estás seguro de solicitar los productos seleccionados?')
                    ->action(function ($records) {
                        // Validar cantidad máxima
                        if ($records->count() > 5) {
                            throw new \Exception('No puedes solicitar más de 5 productos a la vez');
                        }

                        // Procesar solicitud
                        $records->each(function ($record) {
                            $record->update([
                                'estado_prestamo' => 'pendiente',

                            ]);
                        });

                        // Notificación de éxito
                        Notification::make()
                            ->title('Solicitud enviada')
                            ->body('Has solicitado ' . $records->count() . ' productos')
                            ->success()
                            ->send();
                    })
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
