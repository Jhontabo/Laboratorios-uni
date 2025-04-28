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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User Management';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?string $navigationGroup = 'Administration';
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
        return $form->schema([
            Section::make('Personal Information')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('First Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('apellido')
                                ->label('Last Name')
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

                            TextInput::make('telefono')
                                ->label('Phone')
                                ->tel()
                                ->required()
                                ->maxLength(15),
                        ]),
                    TextInput::make('direccion')
                        ->label('Address')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Account Settings')
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

                            Select::make('estado')
                                ->label('Account Status')
                                ->options([
                                    'activo' => 'Active',
                                    'inactivo' => 'Inactive',
                                ])
                                ->default('activo')
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
                TextColumn::make('name')->label('First Name')->searchable()->sortable(),
                TextColumn::make('apellido')->label('Last Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('Roles')->badge()->color('primary')->sortable(),
                TextColumn::make('estado')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'activo' => 'Active',
                        'inactivo' => 'Inactive',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Filter::make('estado')
                    ->label('Account Status')
                    ->form([
                        Select::make('estado')
                            ->options([
                                'activo' => 'Active',
                                'inactivo' => 'Inactive',
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
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil')->color('primary'),
                Action::make('toggleEstado')
                    ->label(fn(Model $record) => $record->estado === 'activo' ? 'Deactivate' : 'Activate')
                    ->icon(fn(Model $record) => $record->estado === 'activo' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn(Model $record) => $record->estado === 'activo' ? 'danger' : 'success')
                    ->action(function (Model $record) {
                        $record->estado = $record->estado === 'activo' ? 'inactivo' : 'activo';
                        $record->save();

                        Notification::make()
                            ->title($record->estado === 'activo' ? 'User activated' : 'User deactivated')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn(Model $record) => $record->estado === 'activo' ? 'Deactivate user' : 'Activate user')
                    ->modalDescription(fn(Model $record) => $record->estado === 'activo'
                        ? 'Are you sure you want to deactivate this user?'
                        : 'Are you sure you want to activate this user?')
                    ->modalSubmitActionLabel(fn(Model $record) => $record->estado === 'activo' ? 'Yes, deactivate' : 'Yes, activate'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            ExcelExport::make('users')
                                ->fromTable()
                                ->withFilename('Users_' . date('d-m-Y'))
                                ->askForWriterType(),
                        ]),
                    BulkAction::make('activate')
                        ->label('Activate selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(fn(Model $record) => $record->update(['estado' => 'activo']));
                            Notification::make()->title('Users activated')->success()->send();
                        })
                        ->requiresConfirmation(),
                    BulkAction::make('deactivate')
                        ->label('Deactivate selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each(function (Model $record) {
                                if (!in_array($record->roles->pluck('name')->first(), ['ADMIN', 'LABORATORISTA'])) {
                                    $record->update(['estado' => 'inactivo']);
                                }
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

