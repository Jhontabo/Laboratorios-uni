<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Gestión de Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej. Juan Carlos')
                                    ->validationMessages([
                                        'required' => 'El nombre es obligatorio',
                                        'max' => 'Máximo 255 caracteres',
                                    ]),

                                TextInput::make('apellido')
                                    ->label('Apellidos')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej. Pérez García')
                                    ->validationMessages([
                                        'required' => 'El apellido es obligatorio',
                                        'max' => 'Máximo 255 caracteres',
                                    ]),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('ejemplo@dominio.com')
                                    ->validationMessages([
                                        'required' => 'El correo es obligatorio',
                                        'email' => 'Debe ser un correo válido',
                                        'unique' => 'Este correo ya está registrado',
                                    ]),

                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->maxLength(15)
                                    ->placeholder('Ej. 3001234567')
                                    ->validationMessages([
                                        'required' => 'El teléfono es obligatorio',
                                        'max' => 'Máximo 15 caracteres',
                                    ]),
                            ]),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('Ej. Calle 123 #45-67')
                            ->validationMessages([
                                'required' => 'La dirección es obligatoria',
                                'max' => 'Máximo 255 caracteres',
                            ]),
                    ]),

                Section::make('Configuración de Cuenta')
                    ->icon('heroicon-o-cog')

                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Select::make('roles')
                                    ->label('Roles')
                                    ->multiple()
                                    ->relationship('roles', 'name')
                                    ->preload()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Debe asignar al menos un rol',
                                    ]),

                                Select::make('estado')
                                    ->label('Estado de la Cuenta')
                                    ->options([
                                        'activo' => 'Activo',
                                        'inactivo' => 'Inactivo',
                                    ])
                                    ->default('activo')
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'El estado es obligatorio',
                                    ]),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombres')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('apellido')
                    ->label('Apellidos')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Filter::make('estado')
                    ->label('Estado de la Cuenta')
                    ->form([
                        Select::make('estado')
                            ->options([
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo',
                            ])
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['estado'], fn($q) => $q->where('estado', $data['estado']))
                    ),

                Filter::make('roles')
                    ->label('Roles')
                    ->form([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['roles'], fn($q) => $q->whereHas('roles', fn($q) => $q->whereIn('id', $data['roles'])))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->tooltip('Editar usuario'),

                Action::make('toggleEstado')
                    ->label(fn(Model $record): string => $record->estado === 'activo' ? 'Desactivar' : 'Activar')
                    ->icon(fn(Model $record): string => $record->estado === 'activo' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn(Model $record): string => $record->estado === 'activo' ? 'danger' : 'success')
                    ->action(function (Model $record): void {
                        $record->estado = $record->estado === 'activo' ? 'inactivo' : 'activo';
                        $record->save();

                        Notification::make()
                            ->title($record->estado === 'activo' ? 'Usuario activado' : 'Usuario desactivado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn(Model $record): string => $record->estado === 'activo' ? 'Desactivar usuario' : 'Activar usuario')
                    ->modalDescription(fn(Model $record): string => $record->estado === 'activo'
                        ? '¿Está seguro de desactivar este usuario?'
                        : '¿Está seguro de activar este usuario?')
                    ->modalSubmitActionLabel(fn(Model $record): string => $record->estado === 'activo' ? 'Sí, desactivar' : 'Sí, activar')
                    ->tooltip(fn(Model $record): string => $record->estado === 'activo' ? 'Desactivar usuario' : 'Activar usuario'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            ExcelExport::make('usuarios')
                                ->fromTable()
                                ->withFilename('Usuarios_' . date('d-m-Y'))
                                ->askForWriterType(),
                        ]),

                    BulkAction::make('activar')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                $record->estado = 'activo';
                                $record->save();
                            });

                            Notification::make()
                                ->title('Usuarios activados')
                                ->body("Se han activado {$records->count()} usuarios")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Activar usuarios seleccionados')
                        ->modalDescription('¿Está seguro de activar los usuarios seleccionados?')
                        ->modalSubmitActionLabel('Sí, activar'),

                    BulkAction::make('desactivar')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                if (!in_array($record->roles->pluck('name')->first(), ['ADMIN', 'LABORATORISTA'])) {
                                    $record->estado = 'inactivo';
                                    $record->save();
                                }
                            });

                            Notification::make()
                                ->title('Usuarios desactivados')
                                ->body("Se han desactivado {$records->count()} usuarios")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Desactivar usuarios seleccionados')
                        ->modalDescription('¿Está seguro de desactivar los usuarios seleccionados? Los administradores no pueden ser desactivados.')
                        ->modalSubmitActionLabel('Sí, desactivar'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Crea tu primer usuario haciendo clic en el botón de arriba')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear usuario')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('name', 'asc')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Relaciones si las necesitas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'apellido', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Correo' => $record->email,
            'Estado' => $record->estado === 'activo' ? 'Activo' : 'Inactivo',
            'Roles' => $record->roles->pluck('name')->join(', '),
        ];
    }
}
