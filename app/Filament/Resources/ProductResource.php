<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Laboratory;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralLabel = 'Productos';
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
        return $form->schema([
            Section::make('Informacion Basica')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Microscopio')
                                ->helperText('Maximo 255 caracteres.'),
                            Textarea::make('description')
                                ->label('Descripcion del producto')
                                ->maxLength(500)
                                ->rows(4)
                                ->placeholder('Caracteristicas del producto o detalles...')
                                ->required(),
                        ]),
                ]),

            Section::make('Especificaiones Tecnicas')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('serial_number')
                                ->label('Numero de Serie')
                                ->required()
                                ->maxLength(255),
                            Select::make('product_type')
                                ->label('Tipo de producto')
                                ->options([
                                    'supply' => 'Suministro',
                                    'equipment' => 'Equipo',
                                ])
                                ->required()
                                ->native(false),
                            Select::make('product_condition')
                                ->label('Condicion del Producto')
                                ->options([
                                    'new' => 'Nuevo',
                                    'used' => 'Usado',
                                    'damaged' => 'Dañado',
                                    'decommissioned' => 'Fuera de Servicio',
                                    'lost' => 'Perdido',
                                ])
                                ->required()
                                ->native(false),
                            Toggle::make('available_for_loan')
                                ->label('Disponible para Prestamo')
                                ->default(false),
                        ]),
                ]),

            Section::make('Inventario y costos')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('available_quantity')
                                ->label('Cantidad disponible')
                                ->numeric()
                                ->required(),
                            TextInput::make('unit_cost')
                                ->label('Costo unitario (COP)')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            DatePicker::make('acquisition_date')
                                ->label('Fecha de adquisición')
                                ->required()
                                ->displayFormat('d/m/Y'),
                            Select::make('laboratory_id')
                                ->label('Laboratorio')
                                ->options(Laboratory::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),

                        ]),
                ]),



            Section::make('Imagen del producto')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('image')
                        ->image()
                        ->imageEditor()
                        ->directory('products')
                        ->disk('public')
                        ->visibility('public')
                        ->helperText('Cargar una imagen representativa'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')
                ->label('Imagen')
                ->size(50)
                ->circular()
                ->toggleable(),

            TextColumn::make('name')
                ->label('Producto')
                ->searchable()
                ->sortable(),

            TextColumn::make('available_quantity')
                ->label('Cantidad'),

            TextColumn::make('product_condition')
                ->label('Condicion')
                ->badge(),

            TextColumn::make('product_type')
                ->label('Tipo')
                ->badge(),

            TextColumn::make('unit_cost')
                ->label('Precio')
                ->money('COP')
                ->sortable(),

            TextColumn::make('acquisition_date')
                ->label('Adquisición')
                ->date('d/m/Y')
                ->sortable(),

            TextColumn::make('laboratory.name')
                ->label('laboratorio')
                ->searchable()
                ->sortable(),


        ])


            ->filters([
                Filter::make('product_type')
                    ->form([
                        Select::make('product_type')
                            ->label('Tipo de prodcuto')
                            ->options([
                                'Supply' => 'Suministro',
                                'Equipment' => 'Equipo',
                            ])
                            ->native(false),  // Usamos 'native(false)' para personalizar el diseño
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['product_type'], fn($q) => $q->where('product_type', $data['product_type']))
                    ),
            ])

            ->actions([  // Actions for individual rows
                EditAction::make()->tooltip('Editar producto'),
            ])
            ->bulkActions([  // Bulk actions for selected rows
                BulkAction::make('markAsLost')
                    ->label('Marcar como perdido')
                    ->action(fn(Collection $records) => $records->each->update(['product_condition' => 'lost']))
                    ->requiresConfirmation()
                    ->modalHeading('Marcar seleccion como perdido'),

                BulkAction::make('decommissionSelected')
                    ->label('Desactivacion')
                    ->action(fn(Collection $records) => $records->each->update(['product_condition' => 'decommissioned']))
                    ->requiresConfirmation()
                    ->modalHeading('Productos selecionados para desactivacion'),

                DeleteBulkAction::make(),  // Bulk delete action
            ])
            ->defaultSort('name', 'asc')
            ->deferLoading()  // Delayed loading
            ->persistFiltersInSession()  // Persist filters in session
            ->persistSearchInSession()  // Persist search in session
            ->striped();  // Striped table rows
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
