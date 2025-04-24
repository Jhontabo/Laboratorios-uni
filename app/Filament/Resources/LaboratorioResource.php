<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaboratorioResource\Pages;
use App\Models\Laboratorio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LaboratorioResource extends Resource
{
    protected static ?string $model = Laboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Gestión de Laboratorios';
    protected static ?string $modelLabel = 'Laboratorio';
    protected static ?string $pluralModelLabel = 'Laboratorios';
    protected static ?string $navigationGroup = 'Inventario y Laboratorio';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Laboratorio')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej. Biología Molecular')
                                    ->helperText('Nombre corto del laboratorio')
                                    ->columnSpan(1),

                                TextInput::make('capacidad')
                                    ->label('Capacidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->step(1)
                                    ->placeholder('Ej. 20')
                                    ->helperText('Personas máx.')
                                    ->columnSpan(1),

                                TextInput::make('ubicacion')
                                    ->label('Ubicación')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Edificio, Piso, Sala')
                                    ->helperText('Ubicación exacta')
                                    ->columnSpan(2),
                            ])
                            ->columns(2),
                    ])
                    ->compact(), // Hace más compacta la sección

                Section::make('Responsable')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('user_id')
                            ->label('Laboratorista')
                            ->options(User::role('LABORATORISTA')->get()->pluck('name', 'user_id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Seleccionar')
                            ->helperText('Responsable asignado'),
                    ])
                    ->compact(), // Hace más compacta la sección
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Laboratorio')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn(Laboratorio $record) => $record->ubicacion),

                TextColumn::make('capacidad')
                    ->badge()
                    ->label('Capacidad')
                    ->formatStateUsing(fn($state): string => "{$state} pers.")
                    ->color(fn($state): string => match (true) {
                        $state > 30 => 'success',
                        $state > 15 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('laboratorista.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->description(fn(Laboratorio $record) => $record->laboratorista->email ?? ''),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('capacidad_alta')
                    ->label('Capacidad alta (>30)')
                    ->query(fn(Builder $query): Builder => $query->where('capacidad', '>', 30))
                    ->toggle(),

                Filter::make('capacidad_media')  // Corregido el nombre (sin espacio)
                    ->label('Capacidad media (15-30)')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('capacidad', [15, 30]))
                    ->toggle(),

                Filter::make('capacidad_baja')
                    ->label('Capacidad baja (<15)')
                    ->query(fn(Builder $query): Builder => $query->where('capacidad', '<', 15))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->tooltip('Editar laboratorio'),


                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Eliminar laboratorio')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Laboratorio eliminado')
                            ->body('El laboratorio ha sido eliminado correctamente.'),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar laboratorios seleccionados')
                        ->modalDescription('¿Está seguro de eliminar los laboratorios seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar'),
                ]),
            ])
            ->emptyStateHeading('No hay laboratorios registrados')
            ->emptyStateDescription('Crea tu primer laboratorio haciendo clic en el botón de arriba')
            ->emptyStateIcon('heroicon-o-beaker')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear laboratorio')
                    ->icon('heroicon-o-plus'),
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
            'index' => Pages\ListLaboratorios::route('/'),
            'create' => Pages\CreateLaboratorio::route('/create'),
            'edit' => Pages\EditLaboratorio::route('/{record}/edit'),
        ];
    }


}
