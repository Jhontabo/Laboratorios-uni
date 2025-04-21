<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoDisponibleResource\Pages;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\ProductoDisponible;
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

class ProductoDisponibleResource extends Resource
{
    protected static ?string $model = ProductoDisponible::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                    ->description(fn(ProductoDisponible $record) => substr($record->descripcion, 0, 50) . '...')
                    ->weight('medium')
                    ->color('primary')
                    ->url(fn(ProductoDisponible $record): string => static::getUrl('view', ['record' => $record])),

                TextColumn::make('cantidad_disponible')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn(ProductoDisponible $record) => $record->cantidad_disponible > 10 ? 'success' : ($record->cantidad_disponible > 0 ? 'warning' : 'danger'))
                    ->icon(fn(ProductoDisponible $record) => $record->cantidad_disponible > 10 ? 'heroicon-o-check-circle' : ($record->cantidad_disponible > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-x-circle')),

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

            ->recordUrl(fn(ProductoDisponible $record): string => static::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('No hay productos registrados')
            ->emptyStateDescription('Crea tu primer producto haciendo clic en el botón de arriba')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear producto')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('') // Oculta el texto
                    ->icon('heroicon-o-eye'), // Icono de vista
            ])
            ->defaultSort('nombre', 'asc')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductoDisponibles::route('/'),
            'view' => Pages\ViewProductoDisponible::route('/{record}'),

        ];
    }
}
