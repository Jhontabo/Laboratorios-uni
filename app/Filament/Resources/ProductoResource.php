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
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $navigationGroup = 'Inventario';


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
                                    ->required(),
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
                                    ])
                                    ->required(),
                            ]),
                    ]),
                Section::make('Imagen del Producto')
                    ->schema([
                        FileUpload::make('imagen')
                            ->label('Imagen del producto')
                            ->image()
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
                TextColumn::make('cantidad_disponible'),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'nuevo' => 'Nuevo',
                            'usado' => 'Usado',
                            'dañado' => 'Dañado',
                            default => '-',
                        };
                    })
                    ->colors([
                        'success' => 'nuevo',
                        'warning' => 'usado',
                        'danger' => 'dañado',
                    ]),
                TextColumn::make('tipo_producto')->label('Tipo de producto'),
                TextColumn::make('laboratorio.ubicacion')->label('Ubicación'),

                TextColumn::make('numero_serie'),
                TextColumn::make('created_at')->date()->label('fecha de cracion'),

            ])
            ->filters([
                // Agrega filtros si es necesario
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
