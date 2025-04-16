<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Laboratorio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Gestión de Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $navigationGroup = 'Inventario y Laboratorio';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'success' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->icon('heroicon-o-information-circle')
                    ->description('Datos principales del producto')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre del Producto')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1)
                                    ->placeholder('Ej. Microscopio Binocular')
                                    ->helperText('Nombre descriptivo del producto')
                                    ->validationMessages([
                                        'required' => 'El nombre del producto es obligatorio',
                                        'max' => 'El nombre no debe exceder los 255 caracteres',
                                    ]),

                                Textarea::make('descripcion')
                                    ->label('Descripción Detallada')
                                    ->maxLength(500)
                                    ->helperText('Descripción técnica o características principales')
                                    ->rows(4)
                                    ->columnSpan(2)
                                    ->placeholder('Describa las características principales del producto...')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Especificaciones Técnicas')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('numero_serie')
                                    ->label('Número de Serie')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Identificador único del producto')
                                    ->placeholder('Ej. SN-2023-001'),

                                Select::make('tipo_producto')
                                    ->label('Tipo de Producto')
                                    ->options([
                                        'suministro' => 'Suministro',
                                        'equipo' => 'Equipo',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('estado')
                                    ->label('Estado Actual')
                                    ->options([
                                        'nuevo' => 'Nuevo',
                                        'usado' => 'Usado',
                                        'dañado' => 'Dañado',
                                        'dado_de_baja' => 'Dado de baja',
                                        'perdido' => 'Perdido',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Inventario y Costos')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('cantidad_disponible')
                                    ->label('Cantidad en Stock')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(1000000)
                                    ->step(1)
                                    ->helperText('Cantidad disponible en inventario'),

                                TextInput::make('costo_unitario')
                                    ->label('Costo Unitario (COP)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(1000000000)
                                    ->step(0.01)
                                    ->helperText('Precio de adquisición por unidad'),

                                DatePicker::make('fecha_adquisicion')
                                    ->label('Fecha de Adquisición')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required()
                                    ->helperText('Fecha cuando se adquirió el producto'),
                            ]),
                    ]),

                Section::make('Ubicación y Clasificación')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('id_categorias')
                                    ->label('Categoría')
                                    ->options(Categoria::all()->pluck('nombre_categoria', 'id_categorias'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->helperText('Seleccione la categoría del producto o escriba una nueva')
                                    ->createOptionForm([
                                        TextInput::make('nombre_categoria')
                                            ->label('Nombre de la nueva categoría')
                                            ->required()
                                            ->maxLength(255)
                                            ->validationMessages([
                                                'required' => 'El nombre de la categoría es obligatorio',
                                                'max' => 'El nombre no debe exceder los 255 caracteres',
                                            ])
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $categoria = Categoria::create($data);

                                        Notification::make()
                                            ->title('Categoría creada')
                                            ->body("La categoría {$categoria->nombre_categoria} fue creada exitosamente")
                                            ->success()
                                            ->send();

                                        return $categoria->id_categorias;
                                    }),

                                Select::make('id_laboratorio')
                                    ->label('Ubicación (Laboratorio)')
                                    ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->helperText('Seleccione la ubicación física'),
                            ]),
                    ]),

                Section::make('Imagen del Producto')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('imagen')
                            ->label('')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('productos')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->helperText('Suba una imagen representativa del producto'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Img')
                    ->size(50)
                    ->circular()
                    ->toggleable(),

                TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Producto $record) => substr($record->descripcion, 0, 50) . '...')
                    ->weight('medium')
                    ->color('primary'),

                TextColumn::make('cantidad_disponible')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn(Producto $record) => $record->cantidad_disponible > 10 ? 'success' : ($record->cantidad_disponible > 0 ? 'warning' : 'danger'))
                    ->icon(fn(Producto $record) => $record->cantidad_disponible > 10 ? 'heroicon-o-check-circle' : ($record->cantidad_disponible > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-x-circle')),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'nuevo' => 'success',
                        'usado' => 'warning',
                        'dañado' => 'danger',
                        'dado_de_baja' => 'gray',
                        'perdido' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
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
                    ->color(fn(string $state): string => match ($state) {
                        'equipo' => 'info',
                        'suministro' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
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
                    ->query(fn(Builder $query): Builder => $query->where('cantidad_disponible', '<=', 10))
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
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['id_laboratorio'], fn($q) => $q->where('id_laboratorio', $data['id_laboratorio']))
                    ),


            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->tooltip('Editar producto'),

                Action::make('darDeBaja')
                    ->label('Baja')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('warning')
                    ->action(function (Producto $record) {
                        $record->update(['estado' => 'dado_de_baja']);

                        Notification::make()
                            ->title('Producto dado de baja')
                            ->body("El producto {$record->nombre} ha sido dado de baja correctamente.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Dar de baja producto')
                    ->modalDescription('¿Está seguro de dar de baja este producto? Esta acción cambiará su estado.')
                    ->modalSubmitActionLabel('Sí, dar de baja')
                    ->tooltip('Dar de baja el producto'),

                Action::make('reportarPerdido')
                    ->label('Perdido')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->action(function (Producto $record) {
                        $record->update(['estado' => 'perdido']);

                        Notification::make()
                            ->title('Producto reportado como perdido')
                            ->body("El producto {$record->nombre} ha sido marcado como perdido.")
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reportar producto como perdido')
                    ->modalDescription('¿Está seguro de marcar este producto como perdido?')
                    ->modalSubmitActionLabel('Sí, reportar')
                    ->tooltip('Reportar como perdido'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Eliminar producto'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('darDeBajaSeleccionados')
                        ->label('Dar de baja seleccionados')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each->update(['estado' => 'dado_de_baja']);

                            Notification::make()
                                ->title('Productos dados de baja')
                                ->body("Se han dado de baja {$records->count()} productos correctamente.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Dar de baja productos seleccionados')
                        ->modalDescription('¿Está seguro de dar de baja los productos seleccionados? Esta acción cambiará su estado.')
                        ->modalSubmitActionLabel('Sí, dar de baja'),

                    BulkAction::make('reportarPerdidosSeleccionados')
                        ->label('Reportar como perdidos')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each->update(['estado' => 'perdido']);

                            Notification::make()
                                ->title('Productos reportados como perdidos')
                                ->body("Se han reportado {$records->count()} productos como perdidos.")
                                ->danger()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Reportar productos como perdidos')
                        ->modalDescription('¿Está seguro de marcar los productos seleccionados como perdidos?')
                        ->modalSubmitActionLabel('Sí, reportar'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->emptyStateHeading('No hay productos registrados')
            ->emptyStateDescription('Crea tu primer producto haciendo clic en el botón de arriba')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear producto')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('nombre', 'asc')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Relaciones si las necesitas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'numero_serie', 'descripcion'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Categoría' => $record->categoria->nombre_categoria ?? '-',
            'Ubicación' => $record->laboratorio->ubicacion ?? '-',
            'Estado' => match ($record->estado) {
                'nuevo' => 'Nuevo',
                'usado' => 'Usado',
                'dañado' => 'Dañado',
                'dado_de_baja' => 'Dado de baja',
                'perdido' => 'Perdido',
                default => $record->estado,
            },
        ];
    }
}
