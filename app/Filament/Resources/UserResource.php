<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';

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
        return $form->schema([
            Section::make('Informacion Personal')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('last_name')
                                ->label('Apellido')
                                ->required()
                                ->maxLength(255),
                        ]),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            TextInput::make('phone')
                                ->label('Telefono')
                                ->tel()
                                ->required()
                                ->maxLength(15),
                        ]),
                    TextInput::make('address')
                        ->label('Direccion')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Ajustes de cuenta')
                ->icon('heroicon-o-cog')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Select::make('roles')
                                ->label('Roles')
                                ->multiple()
                                ->relationship('roles', 'name')
                                ->preload()
                                ->required(),

                            Select::make('status')
                                ->label('Estado de cuenta')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                ])
                                ->default('Active')
                                ->required()
                                ->native(false),
                        ])
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('last_name')->label('Apellido')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('Roles')->badge()->color('primary')->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Filter::make('status')
                    ->label('Estado de cuenta')
                    ->form([
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder =>
                        $query->when($data['status'], fn($q) => $q->where('status', $data['status']))
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
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil')->color('primary'),
                Action::make('togglestatus')
                    ->label(fn(Model $record) => $record->status === 'active' ? 'Desactivar' : 'Activar')
                    ->icon(fn(Model $record) => $record->status === 'active' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn(Model $record) => $record->status === 'active' ? 'danger' : 'success')
                    ->action(function (Model $record) {
                        // Cambio aquí: usé $record->status en lugar de $record->estado
                        $record->status = $record->status === 'active' ? 'inactive' : 'active'; // Cambié 'estado' a 'status'
                        $record->save();

                        Notification::make()
                            ->title($record->status === 'active' ? 'Usuario activado' : 'Usuario desactivado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn(Model $record) => $record->status === 'active' ? 'Desactivar usuario' : 'Activar usuario')
                    ->modalDescription(fn(Model $record) => $record->status === 'active'
                        ? 'Estas seguro que quieres desactivar este usuario?'
                        : 'Estas seguro que quieres activar este usuario?')
                    ->modalSubmitActionLabel(fn(Model $record) => $record->status === 'active' ? 'Si, desactivar' : 'Si, activar'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    BulkAction::make('deactivate')
                        ->label('Deactivate selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each(function (Model $record) {
                                // Eliminar la condición para no desactivar ciertos roles si deseas permitir la desactivación
                                // Aquí solo se actualiza el estado de los usuarios seleccionados
                                $record->update(['status' => 'inactive']);
                            });
                            Notification::make()->title('Users deactivated')->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete selected')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->emptyStateHeading('No users registered')
            ->emptyStateDescription('Create your first user by clicking the button above')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create user')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
