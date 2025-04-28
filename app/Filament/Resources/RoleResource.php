<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationLabel = 'Role Management';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Role Information')
                ->description('Define the basic details of the role')
                ->icon('heroicon-o-identification')
                ->schema([
                    TextInput::make('name')
                        ->label('Role Name')
                        ->placeholder('Example: ADMIN, USER, GUEST')
                        ->autocapitalize('words')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->afterStateUpdated(fn($state, $set) => $set('name', strtoupper($state)))
                        ->helperText('Use only uppercase letters and underscores (e.g., ADMIN_USERS)')
                        ->columnSpanFull(),
                ]),

            Section::make('Assign Permissions')
                ->description('Select associated permissions')
                ->icon('heroicon-o-lock-closed')
                ->schema([
                    Select::make('permissions')
                        ->label('Permissions')
                        ->relationship('permissions', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->maxItems(50)
                        ->helperText('Search and select multiple permissions.')
                        ->loadingMessage('Loading permissions...')
                        ->noSearchResultsMessage('No permissions found.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->description(fn(Role $record) => $record->permissions->count() . ' permissions assigned'),

                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 5 => 'info',
                        $state <= 10 => 'primary',
                        default => 'success',
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ])
            ->emptyStateHeading('No roles found')
            ->emptyStateDescription('Create your first role by clicking the button above.')
            ->emptyStateIcon('heroicon-o-shield-exclamation')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Role')
                    ->icon('heroicon-o-plus'),
            ])
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

