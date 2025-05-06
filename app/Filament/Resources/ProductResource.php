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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Builder as FormBuilder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tab;
use Filament\Forms\Components\View;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Colors\Color;
use Filament\Tables\Enums\ActionsPosition;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralLabel = 'Productos';
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
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Información Básica')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nombre del Producto')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(1)
                                        ->placeholder('Ej: Microscopio Digital')
                                        ->helperText('Nombre descriptivo del producto')
                                        ->live(onBlur: true),

                                    Select::make('laboratory_id')
                                        ->label('Laboratorio Asignado')
                                        ->options(Laboratory::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpan(1),
                                ]),

                            RichEditor::make('description')
                                ->label('Descripción Detallada')
                                ->maxLength(1000)
                                ->columnSpanFull()
                                ->fileAttachmentsDirectory('products/attachments')
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->placeholder('Describa las características principales, especificaciones técnicas y cualquier detalle relevante...'),
                        ]),

                    Step::make('Especificaciones Técnicas')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('serial_number')
                                        ->label('Número de Serie')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->validationMessages([
                                            'unique' => 'Este número de serie ya está registrado',
                                        ]),

                                    Select::make('product_type')
                                        ->label('Tipo de Producto')
                                        ->options([
                                            'supply' => 'Suministro',
                                            'equipment' => 'Equipo',
                                            'chemical' => 'Reactivo Químico',
                                            'glassware' => 'Material de Vidrio',
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->live(),

                                    Select::make('product_condition')
                                        ->label('Condición Actual')
                                        ->options([
                                            'new' => 'Nuevo',
                                            'used' => 'Usado - Buen Estado',
                                            'used_worn' => 'Usado - Desgaste Normal',
                                            'damaged' => 'Dañado - Reparación Necesaria',
                                            'decommissioned' => 'Fuera de Servicio',
                                            'lost' => 'Perdido/Robo',
                                        ])
                                        ->required()
                                        ->native(false),
                                ]),

                            Fieldset::make('Estado de Disponibilidad')
                                ->schema([
                                    Toggle::make('available_for_loan')
                                        ->label('Disponible para Préstamo')
                                        ->default(true)
                                        ->inline(false)
                                        ->onColor('success')
                                        ->offColor('danger'),


                                ]),
                        ]),

                    Step::make('Inventario y Costos')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('available_quantity')
                                        ->label('Cantidad en Inventario')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->step(1)
                                        ->suffix('unidades'),


                                    TextInput::make('unit_cost')
                                        ->label('Costo Unitario')
                                        ->numeric()
                                        ->prefix('$')
                                        ->required()
                                        ->step(0.01),

                                    DatePicker::make('acquisition_date')
                                        ->label('Fecha de Adquisición')
                                        ->required()
                                        ->displayFormat('d/m/Y')
                                        ->maxDate(now()),
                                ]),

                            Placeholder::make('cost_placeholder')
                                ->content(function (Get $get) {
                                    $quantity = $get('available_quantity') ?? 0;
                                    $cost = $get('unit_cost') ?? 0;
                                    $total = $quantity * $cost;

                                    return "Costo total estimado: $" . number_format($total, 2);
                                })
                                ->hidden(fn(Get $get) => empty($get('available_quantity')) || empty($get('unit_cost'))),
                        ]),

                    Step::make('Documentación e Imágenes')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Imagen Principal')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->directory('products/images')
                                ->disk('public')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->openable()
                                ->downloadable()
                                ->previewable()
                                ->helperText('Imagen representativa del producto (max 2MB)')
                                ->columnSpanFull(),

                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->size(60)
                    ->circular()
                    ->defaultImageUrl(fn($record) => $record->product_type === 'equipment'
                        ? asset('images/default-equipment.png')
                        : asset('images/default-supply.png')),

                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->wrap()
                    ->tooltip(function (Product $record) {
                        return "Descripción completa: " . $record->description;
                    }),

                TextColumn::make('available_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->available_quantity <= ($record->minimum_stock ?? 0)) {
                            return 'danger';
                        }
                        return 'success';
                    })
                    ->icon(function ($record) {
                        if ($record->available_quantity <= ($record->minimum_stock ?? 0)) {
                            return 'heroicon-o-exclamation-triangle';
                        }
                        return null;
                    })
                    ->iconPosition(IconPosition::After)
                    ->summarize([
                        Sum::make()->label('Total Items'),
                        Average::make()->label('Promedio'),
                    ]),

                TextColumn::make('product_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'equipment' => 'info',
                        'supply' => 'success',
                        'chemical' => 'warning',
                        'glassware' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'equipment' => 'Equipo',
                        'supply' => 'Suministro',
                        'chemical' => 'Reactivo',
                        'glassware' => 'Vidriería',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('product_condition')
                    ->label('Condición')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'success',
                        'used' => 'info',
                        'used_worn' => 'warning',
                        'damaged', 'decommissioned', 'lost' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'new' => 'Nuevo',
                        'used' => 'Buen Estado',
                        'used_worn' => 'Desgaste',
                        'damaged' => 'Dañado',
                        'decommissioned' => 'Inactivo',
                        'lost' => 'Perdido',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Costo Unitario')
                    ->money('COP')
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total')->money('COP'),
                        Average::make()->label('Promedio')->money('COP'),
                    ]),

                ToggleColumn::make('available_for_loan')
                    ->label('Préstamo')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('laboratory.name')
                    ->label('Ubicación')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('product_type')
                    ->label('Tipo de Producto')
                    ->options([
                        'equipment' => 'Equipo',
                        'supply' => 'Suministro',
                        'chemical' => 'Reactivo Químico',
                        'glassware' => 'Material de Vidrio',
                    ])
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('product_condition')
                    ->label('Condición')
                    ->options([
                        'new' => 'Nuevo',
                        'used' => 'Buen Estado',
                        'used_worn' => 'Desgaste Normal',
                        'damaged' => 'Dañado',
                        'decommissioned' => 'Inactivo',
                        'lost' => 'Perdido',
                    ])
                    ->multiple(),

                SelectFilter::make('laboratory_id')
                    ->label('Laboratorio')
                    ->options(Laboratory::all()->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),

                TernaryFilter::make('available_for_loan')
                    ->label('Disponible para Préstamo')
                    ->trueLabel('Solo disponibles')
                    ->falseLabel('No disponibles'),

                Filter::make('low_stock')
                    ->label('Stock Bajo')
                    ->query(fn(Builder $query): Builder => $query->whereColumn('available_quantity', '<=', 'minimum_stock'))
                    ->default(false),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                    ->tooltip('Acciones')
                    ->icon('heroicon-s-cog-6-tooth')
                    ->color('primary'),
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                BulkAction::make('markAsLost')
                    ->label('Marcar como Perdido')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('warning')
                    ->action(fn(Collection $records) => $records->each->update(['product_condition' => 'lost']))
                    ->requiresConfirmation()
                    ->modalHeading('Marcar productos seleccionados como perdidos')
                    ->modalDescription('¿Está seguro de marcar estos productos como perdidos?')
                    ->modalSubmitActionLabel('Sí, marcar como perdidos'),

                BulkAction::make('decommissionSelected')
                    ->label('Dar de Baja')
                    ->icon('heroicon-o-no-symbol')
                    ->color('primary')
                    ->form([
                        Select::make('reason')
                            ->label('Motivo de baja')
                            ->options([
                                'maintenance' => 'Mantenimiento',
                                'damaged' => 'Dañado',
                            ])
                            ->required()
                            ->live(),

                        Select::make('responsible_student_id')
                            ->label('Estudiante responsable')
                            ->options(function (callable $get) {
                                // Solo se muestra si el motivo es "Dañado"
                                if ($get('reason') !== 'damaged') {
                                    return [];
                                }


                                return \App\Models\User::query()
                                    ->whereHas('roles', function ($query) {
                                        $query->where('name', 'ESTUDIANTE'); // Ajusta 'estudiante' al nombre exacto de tu rol
                                    })
                                    ->selectRaw("id, CONCAT(name, ' ', last_name) as full_name")
                                    ->pluck('full_name', 'id');
                            })
                            ->searchable()
                            ->hidden(fn(callable $get) => $get('reason') !== 'damaged'),

                        Textarea::make('notes')
                            ->label('Observaciones')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        foreach ($records as $record) {
                            $record->update([
                                'product_condition' => 'decommissioned',
                                'decommission_reason' => $data['reason'],
                                'responsible_student_id' => $data['reason'] === 'damaged'
                                    ? $data['responsible_student_id']
                                    : null,
                                'decommission_notes' => $data['notes'],
                            ]);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Dar de baja productos seleccionados')
                    ->modalDescription('¿Está seguro de dar de baja los productos seleccionados?')
                    ->modalSubmitActionLabel('Confirmar baja'),
                DeleteBulkAction::make()
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crear Nuevo Producto')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('name', 'asc')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped()
            ->groups([
                'laboratory.name',
                'product_type',
                'product_condition',
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->groupRecordsTriggerAction(
                fn(Action $action) => $action
                    ->label('Agrupar registros')
                    ->icon('heroicon-o-bars-arrow-down')
            );
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
