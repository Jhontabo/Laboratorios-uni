<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolResource\Pages;
use App\Filament\Resources\RolResource\RelationManagers;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\FontWeight;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Gestión de Roles';
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralLabel = 'Roles';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns(2)
                    ->schema([

                        Section::make('Asignación de Permisos')
                            ->description('Seleccione los permisos asociados')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Select::make('permissions')
                                    ->label('Seleccione los permisos')
                                    ->placeholder('Busque y seleccione permisos')
                                    ->multiple()
                                    ->relationship('permissions', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Use el buscador para encontrar permisos')
                                    ->columnSpanFull()
                                    ->loadingMessage('Cargando permisos...')
                                    ->noSearchResultsMessage('No se encontraron permisos')
                                    ->maxItems(50),
                            ]),
                        // Columna Izquierda - Información del Rol
                        Section::make('Información del Rol')
                            ->description('Defina los detalles básicos del rol')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Rol*')
                                    ->placeholder('Ejemplo: ADMINISTRADOR, USUARIO, INVITADO')
                                    ->autocapitalize('words')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Use solo mayúsculas y guiones bajos (ej: ADMIN_USUARIOS)')
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('name', strtoupper($state)); // Auto-mayúsculas al escribir
                                    })
                            ]),

                        // Columna Derecha - Asignación de Permisos

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('NOMBRE DEL ROL')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->description(fn(Rol $record) => $record->permissions->count() . ' permisos asignados'),

                TextColumn::make('permissions_count')
                    ->label('PERMISOS')
                    ->counts('permissions')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 5 => 'info',
                        $state <= 10 => 'primary',
                        default => 'success',
                    }),

                TextColumn::make('created_at')
                    ->label('CREACIÓN')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('ACTUALIZACIÓN')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])



            ->filters([])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Nuevo Rol')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateDescription('No se encontraron roles registrados')
            ->emptyStateIcon('heroicon-o-shield-exclamation')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRols::route('/'),
            'create' => Pages\CreateRol::route('/create'),
            'edit' => Pages\EditRol::route('/{record}/edit'),
        ];
    }
}
