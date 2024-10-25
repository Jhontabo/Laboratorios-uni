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

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function getPluralLabel(): string
    {
        return 'Inventario'; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('descripcion'),
                Select::make('id_categorias')  // Aquí usas el campo id de la categoría
                    ->label('Categoría')
                    ->options(Categoria::all()->pluck('nombre_categoria', 'id_categorias'))
                    ->required(),

                TextInput::make('numero_serie')
                    ->label('Número de serie')
                    ->required(),
                TextInput::make('cantidad_disponible')
                    ->numeric()
                    ->required()
                    ->rules('gt:0')
                    ->minValue(1),
                    Select::make('id_laboratorio') 
                    ->label('Ubicacion (Laboratorio)')
                    ->options(Laboratorio::all()->pluck('ubicacion', 'id_laboratorio'))  
                    ->searchable()
                    ->required(),
                Select::make('estado')
                    ->label("Estado")
                    ->options([
                        'nuevo' => 'Nuevo',
                        'usado' => 'Usado',
                        'dañado' => 'Dañado',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('laboratorio.ubicacion')->label('Ubicación'),

                TextColumn::make('numero_serie'),
                TextColumn::make('created_at')->date(),
            ])
            ->filters([
                // Agrega filtros si es necesario
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
