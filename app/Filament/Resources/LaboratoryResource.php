<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaboratoryResource\Pages;
use App\Models\Laboratory;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaboratoryResource extends Resource
{
    protected static ?string $model = Laboratory::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Laboratory Management';
    protected static ?string $modelLabel = 'Laboratory';
    protected static ?string $pluralModelLabel = 'Laboratories';
    protected static ?string $navigationGroup = 'Inventory and Laboratory';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Laboratory Information')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex. Molecular Biology')
                                    ->helperText('Short name for the laboratory'),

                                Forms\Components\TextInput::make('capacity')
                                    ->label('Capacity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->step(1)
                                    ->placeholder('Ex. 20')
                                    ->helperText('Maximum number of people'),

                                Forms\Components\TextInput::make('location')
                                    ->label('Location')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Building, Floor, Room')
                                    ->helperText('Exact location'),
                            ])
                            ->columns(2),
                    ])
                    ->compact(),

                Forms\Components\Section::make('Manager')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Laboratory Manager')
                            ->options(User::role('LABORATORISTA')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Select a manager')
                            ->helperText('Assigned responsible person'),
                    ])
                    ->compact(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Laboratory')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn(Laboratory $record) => $record->location),

                Tables\Columns\TextColumn::make('capacity')
                    ->badge()
                    ->label('Capacity')
                    ->formatStateUsing(fn($state): string => "{$state} people")
                    ->color(fn($state): string => match (true) {
                        $state > 30 => 'success',
                        $state > 15 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Manager')
                    ->sortable()
                    ->searchable()
                    ->description(fn(Laboratory $record) => $record->manager?->email ?? ''),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('high_capacity')
                    ->label('High Capacity (>30)')
                    ->query(fn(Builder $query): Builder => $query->where('capacity', '>', 30)),

                Tables\Filters\Filter::make('medium_capacity')
                    ->label('Medium Capacity (15-30)')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('capacity', [15, 30])),

                Tables\Filters\Filter::make('low_capacity')
                    ->label('Low Capacity (<15)')
                    ->query(fn(Builder $query): Builder => $query->where('capacity', '<', 15)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->tooltip('Edit laboratory'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Delete laboratory')
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Laboratory deleted')
                            ->body('The laboratory was successfully deleted.'),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Delete selected')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete selected laboratories')
                    ->modalDescription('Are you sure you want to delete the selected laboratories? This action cannot be undone.'),
            ])
            ->emptyStateHeading('No laboratories yet')
            ->emptyStateDescription('Create your first laboratory by clicking the button above')
            ->emptyStateIcon('heroicon-o-beaker')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create laboratory')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('name', 'asc')
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaboratories::route('/'),
            'create' => Pages\CreateLaboratory::route('/create'),
            'edit' => Pages\EditLaboratory::route('/{record}/edit'),
        ];
    }
}

