<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaResource\Pages;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Gestion de Categoría';
    protected static ?string $modelLabel = 'Categoría';
    protected static ?string $pluralModelLabel = 'Categorías';
    protected static ?string $navigationGroup = 'Inventario y Laboratorio';
    protected static ?int $navigationSort = 2;

    public static function getBreadcrumb(): string
    {
        return static::$pluralModelLabel;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Categoría')
                    ->description('Complete los detalles de la categoría')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_categoria')
                            ->required()
                            ->label('Nombre de la Categoría')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Ingrese el nombre de la categoría (máximo 255 caracteres)')
                            ->placeholder('Ej. Electrónicos, Ropa, Hogar')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'El nombre de la categoría es obligatorio',
                                'max' => 'El nombre no debe exceder los 255 caracteres',
                                'unique' => 'Esta categoría ya existe',
                            ]),
                    ])
                    ->columns(1)
                    ->icon('heroicon-o-tag'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_categoria')
                    ->label('Nombre de Categoría')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Categoria $record) => 'Creado el ' . $record->created_at->format('d/m/Y'))
                    ->weight('medium')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('recientes')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subMonth()))
                    ->label('Recientes (último mes)'),

                Tables\Filters\Filter::make('creado_entre')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('success')
                    ->tooltip('Editar categoría'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar categoría')
                    ->modalDescription('¿Estás seguro de querer eliminar esta categoría? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->tooltip('Eliminar categoría'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar categorías seleccionadas')
                        ->modalDescription('¿Estás seguro de querer eliminar las categorías seleccionadas? Esta acción no se puede deshacer.'),
                ]),
            ])
            ->emptyStateHeading('No hay categorías aún')
            ->emptyStateDescription('Crea tu primera categoría haciendo clic en el botón de arriba')
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear categoría')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('nombre_categoria', 'asc')
            ->striped()
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getRelations(): array
    {
        return [
            // Aquí puedes agregar relaciones si es necesario
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre_categoria'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Categoría' => $record->nombre_categoria,
            'Creado' => $record->created_at->format('d/m/Y'),
        ];
    }
}
