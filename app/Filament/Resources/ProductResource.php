<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
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
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 1;

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
                            TextInput::make('name')
                                ->label('Product Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Microscope')
                                ->helperText('Maximum 255 characters.'),
                            Textarea::make('description')
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
                            TextInput::make('serial_number')
                                ->label('Serial Number')
                                ->required()
                                ->maxLength(255),
                            Select::make('product_type')
                                ->label('Product Type')
                                ->options([
                                    'supply' => 'Supply',
                                    'equipment' => 'Equipment',
                                ])
                                ->required()
                                ->native(false),
                            Select::make('product_condition')
                                ->label('Product Condition')
                                ->options([
                                    'new' => 'New',
                                    'used' => 'Used',
                                    'damaged' => 'Damaged',
                                    'decommissioned' => 'Decommissioned',
                                    'lost' => 'Lost',
                                ])
                                ->required()
                                ->native(false),
                            Toggle::make('available_for_loan')
                                ->label('Available for Loan')
                                ->default(false),
                        ]),
                ]),

            Section::make('Inventory and Costs')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('available_quantity')
                                ->label('Stock Quantity')
                                ->numeric()
                                ->required(),
                            TextInput::make('unit_cost')
                                ->label('Unit Cost (COP)')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            DatePicker::make('acquisition_date')
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

                            Select::make('laboratory_id')
                                ->label('Laboratory Location')
                                ->options(Laboratory::all()->pluck('location', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                ]),

            Section::make('Product Image')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('image')
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
            ImageColumn::make('image')
                ->label('Image')
                ->size(50)
                ->circular()
                ->toggleable(),

            TextColumn::make('name')
                ->label('Product')
                ->searchable()
                ->sortable(),

            TextColumn::make('available_quantity')
                ->label('Stock')
                ->sortable(),

            TextColumn::make('product_condition')
                ->label('Condition')
                ->badge(),

            TextColumn::make('product_type')
                ->label('Type')
                ->badge(),

            TextColumn::make('unit_cost')
                ->label('Price')
                ->money('COP')
                ->sortable(),

            TextColumn::make('acquisition_date')
                ->label('Acquisition')
                ->date('d/m/Y')
                ->sortable(),

            TextColumn::make('laboratory.location')
                ->label('Location')
                ->searchable()
                ->sortable(),


        ])


            ->filters([
                Filter::make('product_type')
                    ->label('Product Type')
                    ->form([
                        Select::make('product_type')  // Aquí cambiamos 'status' por 'type'
                            ->options([
                                'Supply' => 'Supply',
                                'Equipment' => 'Equipment',
                            ])
                            ->native(false),  // Usamos 'native(false)' para personalizar el diseño
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['product_type'], fn($q) => $q->where('product_type', $data['product_type']))
                    ),
            ])

            ->actions([  // Actions for individual rows
                EditAction::make()->tooltip('Edit product'),
            ])
            ->bulkActions([  // Bulk actions for selected rows
                BulkAction::make('markAsLost')
                    ->label('Mark as Lost')
                    ->action(fn(Collection $records) => $records->each->update(['product_condition' => 'lost']))
                    ->requiresConfirmation()
                    ->modalHeading('Mark selected as lost'),

                BulkAction::make('decommissionSelected')
                    ->label('Decommission')
                    ->action(fn(Collection $records) => $records->each->update(['product_condition' => 'decommissioned']))
                    ->requiresConfirmation()
                    ->modalHeading('Decommission selected products'),

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
