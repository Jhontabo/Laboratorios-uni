<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Categoria;
use App\Models\Laboratorio;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $navigationGroup = 'Inventario';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Grid::make(2)  // Dos columnas para una mejor distribución del espacio
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre del producto')
                                    ->required()
                                    ->maxLength('255')
                                    ->helperText('Máximo 255 caracteres'),
                                Textarea::make('descripcion')
                                    ->label('Descripción del producto')
                                    ->maxLength(500)
                                    ->helperText('Máximo 500 caracteres')
                                    ->rows(4)
                                    ->cols(20)
                                    ->required()
                            ]),
                    ]),
                Section::make('Detalles del Producto')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('numero_serie')
                                    ->label('Número de serie')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Máximo 255 caracteres'),
                                Select::make('tipo_producto')
                                    ->label('Tipo de Producto')
                                    ->options([
                                        'suministro' => 'Suministro',
                                        'equipo' => 'Equipo'
                                    ])
                                    ->required(),
                                TextInput::make('cantidad_disponible')
                                    ->label('Cantidad disponible')
                                    ->numeric()
                                    ->required()
                                    ->rules('gt:0|max:1000000')
                                    ->minValue(1)
                                    ->maxValue(1000000)
                                    ->helperText('La cantidad debe ser mayor a 0'),
                                TextInput::make('costo_unitario')
                                    ->label('Costo Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->rules('gt:0|max:1000000000')
                                    ->minValue(1)
                                    ->maxValue(1000000)
                                    ->helperText('Ingrese el costo unitario del producto'),
                                DatePicker::make('fecha_adquisicion')
                                    ->label('Fecha de Adquisición')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required()
                                    ->columns(3)
                                    ->helperText('Seleccione la fecha de adquisición del producto'),
                            ]),
                    ]),
                Section::make('Ubicación y Estado')
                    ->schema([
                        Grid::make(2)  // Dos columnas
                            ->schema([
                                Select::make('id_categorias')
                                    ->label('Categoría')
                                    ->options(Categoria::all()->pluck('nombre_categoria', 'id_categorias'))
                                    ->required(),
                                Select::make('id_laboratorio')
                                    ->label('Ubicación (Laboratorio)')
                                    ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio'))
                                    ->searchable()
                                    ->required(),
                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'nuevo' => 'Nuevo',
                                        'usado' => 'Usado',
                                        'dañado' => 'Dañado',
                                        'dado_de_baja' => 'Dado de baja',
                                        'perdido' => 'Perdido',
                                    ])
                                    ->required(),
                            ]),
                    ]),
                Section::make('Imagen del Producto')
                    ->schema([
                        FileUpload::make('imagen')
                            ->label('Imagen del producto')
                            ->image()
                            ->imageEditor()
                            ->directory('productos')
                            ->disk('public')
                            ->visibility('public')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->sortable(),
                TextColumn::make('nombre'),
                TextColumn::make('cantidad_disponible')->label('Disponible'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'nuevo' => 'Nuevo',
                            'usado' => 'Usado',
                            'dañado' => 'Dañado',
                            'dado_de_baja' => 'Dado de baja',
                            'perdido' => 'Perdido',

                            default => '-',
                        };
                    })
                    ->colors([
                        'success' => 'nuevo',
                        'warning' => 'usado',
                        'danger' => 'dañado',
                        'warning' => 'dado_de_baja',
                        'danger' => 'perdido',
                    ]),

                TextColumn::make('numero_serie'),
                TextColumn::make('costo_unitario')
                    ->label('Costo Unitario')
                    ->money('cop') // Formatea el valor como moneda colombiana
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipo_producto')
                    ->searchable()->label('Tipo de producto'),
                TextColumn::make('laboratorio.ubicacion')->label('Ubicación')->searchable(),
                TextColumn::make('fecha_adquisicion')->label('Fecha de Adquisición'),
                TextColumn::make('created_at')->date()->label('fecha de cracion'),

            ])

            ->filters([
                Filter::make('nombre')
                    ->form([
                        TextInput::make('nombre')->label('Nombre')->placeholder('Buscar por nombre'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['nombre'], fn($q) => $q->where('nombre', 'like', "%{$data['nombre']}%"))),

                Filter::make('numero_serie')
                    ->form([
                        TextInput::make('numero_serie')->label('Numero de serie')->placeholder('Buscar por numero de serie'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['numero_serie'], fn($q) => $q->where('numero_serie', 'like', "%{$data['numero_serie']}%"))),


                Filter::make('estado')
                    ->label('Estado')
                    ->form([
                        Select::make('estado')
                            ->options([
                                'nuevo' => 'Nuevo',
                                'usado' => 'Usado',
                                'dañado' => 'Dañado',
                                'dado_de_baja' => 'Dado de baja',
                                'perdido' => 'perdido',
                            ]),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['estado'], fn($q) => $q->where('estado', $data['estado']))),

                Filter::make('categoria')
                    ->label('Categoría')
                    ->form([
                        Select::make('id_categorias')
                            ->label('Categoría')
                            ->options(Categoria::all()->pluck('nombre_categoria', 'id_categorias')),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['id_categorias'], fn($q) => $q->where('id_categorias', $data['id_categorias']))),

                Filter::make('ubicacion')
                    ->label('Ubicación')
                    ->form([
                        Select::make('id_laboratorio')
                            ->label('Ubicación')
                            ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio')),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['id_laboratorio'], fn($q) => $q->where('id_laboratorio', $data['id_laboratorio']))),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),


                Action::make('darDeBaja')
                    ->label('Dar de Baja')
                    ->action(function (Model $record) {
                        $record->update(['estado' => 'dado_de_baja']);

                        Notification::make()
                            ->title('Producto dado de baja')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('warning'),

                Action::make('reportarPerdido')
                    ->label('Reportar como Perdido')
                    ->action(function (Model $record) {
                        $record->update(['estado' => 'perdido']);

                        Notification::make()
                            ->title('Producto reportado como perdido')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('darDeBajaSeleccionados')
                        ->label('Dar de Baja')
                        ->action(function (Collection $records) {
                            $records->each(fn(Model $record) => $record->update(['estado' => 'dado_de_baja']));

                            Notification::make()
                                ->title('Productos dados de baja')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->color('warning'),

                    BulkAction::make('reportarPerdidosSeleccionados')
                        ->label('Reportar como Perdidos')
                        ->action(function (Collection $records) {
                            $records->each(fn(Model $record) => $record->update(['estado' => 'perdido']));

                            Notification::make()
                                ->title('Productos reportados como perdidos')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->color('danger'),


                ]),


            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
}
