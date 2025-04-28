<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Models\Laboratory;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Product Management';
    protected static ?string $modelLabel = 'Product';
    protected static ?string $pluralModelLabel = 'Products';
    protected static ?string $navigationGroup = 'Inventory and Laboratory';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'nombre'; // Aún depende de tu base de datos

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
            Section::make('Basic Information')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Product Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Microscope')
                                ->helperText('Maximum 255 characters.'),
                            Textarea::make('descripcion')
                                ->label('Product Description')
                                ->maxLength(500)
                                ->rows(4)
                                ->placeholder('Product features or details...')
                                ->required(),
                        ]),
                ]),

            Section::make('Technical Specifications')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('numero_serie')
                                ->label('Serial Number')
                                ->required()
                                ->maxLength(255),
                            Select::make('tipo_producto')
                                ->label('Product Type')
                                ->options([
                                    'suministro' => 'Supply',
                                    'equipo' => 'Equipment',
                                ])
                                ->required()
                                ->native(false),
                            Select::make('estado_producto')
                                ->label('Product Condition')
                                ->options([
                                    'nuevo' => 'New',
                                    'usado' => 'Used',
                                    'dañado' => 'Damaged',
                                    'dado_de_baja' => 'Decommissioned',
                                    'perdido' => 'Lost',
                                ])
                                ->required()
                                ->native(false),
                            Toggle::make('disponible_para_prestamo')
                                ->label('Available for Loan')
                                ->default(false),
                        ]),
                ]),

            Section::make('Inventory and Costs')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('cantidad_disponible')
                                ->label('Stock Quantity')
                                ->numeric()
                                ->required(),
                            TextInput::make('costo_unitario')
                                ->label('Unit Cost (COP)')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            DatePicker::make('fecha_adquisicion')
                                ->label('Acquisition Date')
                                ->required()
                                ->displayFormat('d/m/Y'),
                        ]),
                ]),

            Section::make('Location and Classification')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('id_categorias')
                                ->label('Category')
                                ->options(Category::all()->pluck('nombre_categoria', 'id_categorias'))
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('id_laboratorio')
                                ->label('Laboratory Location')
                                ->options(Laboratory::all()->pluck('ubicacion', 'id_laboratorio'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                ]),

            Section::make('Product Image')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('imagen')
                        ->image()
                        ->imageEditor()
                        ->directory('products')
                        ->disk('public')
                        ->visibility('public')
                        ->helperText('Upload a representative image'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('imagen')
                ->label('Image')
                ->size(50)
                ->circular()
                ->toggleable(),

            TextColumn::make('nombre')
                ->label('Product')
                ->searchable()
                ->sortable(),

            TextColumn::make('cantidad_disponible')
                ->label('Stock')
                ->sortable(),

            TextColumn::make('estado_producto')
                ->label('Condition')
                ->badge(),

            TextColumn::make('tipo_producto')
                ->label('Type')
                ->badge(),

            TextColumn::make('costo_unitario')
                ->label('Price')
                ->money('COP')
                ->sortable(),

            TextColumn::make('fecha_adquisicion')
                ->label('Acquisition')
                ->date('d/m/Y')
                ->sortable(),

            TextColumn::make('laboratorio.ubicacion')
                ->label('Location')
                ->searchable()
                ->sortable(),

            TextColumn::make('categoria.nombre_categoria')
                ->label('Category')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Filter::make('low_stock')
                ->label('Low Stock (<=10)')
                ->query(fn(Builder $query) => $query->where('cantidad_disponible', '<=', 10))
                ->toggle(),

            Filter::make('by_laboratory')
                ->label('By Laboratory')
                ->form([
                    Select::make('id_laboratorio')
                        ->options(Laboratory::all()->pluck('ubicacion', 'id_laboratorio'))
                        ->searchable()
                        ->preload(),
                ])
                ->query(fn(Builder $query, array $data) => $query->when($data['id_laboratorio'], fn($q) => $q->where('id_laboratorio', $data['id_laboratorio']))),
        ])
        ->actions([
            EditAction::make()->tooltip('Edit product'),
            DeleteBulkAction::make()->tooltip('Delete selected'),
        ])
        ->bulkActions([
            BulkAction::make('markAsLost')
                ->label('Mark as Lost')
                ->action(fn(Collection $records) => $records->each->update(['estado_producto' => 'perdido']))
                ->requiresConfirmation()
                ->modalHeading('Mark selected as lost'),

            BulkAction::make('decommissionSelected')
                ->label('Decommission')
                ->action(fn(Collection $records) => $records->each->update(['estado_producto' => 'dado_de_baja']))
                ->requiresConfirmation()
                ->modalHeading('Decommission selected products'),

            DeleteBulkAction::make(),
        ])
        ->defaultSort('nombre', 'asc')
        ->deferLoading()
        ->persistFiltersInSession()
        ->persistSearchInSession()
        ->striped();
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

