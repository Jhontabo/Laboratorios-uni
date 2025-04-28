<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Category Management';
    protected static ?string $modelLabel = 'Category';
    protected static ?string $pluralModelLabel = 'Categories';
    protected static ?string $navigationGroup = 'Inventory and Laboratory';
    protected static ?int $navigationSort = 2;

    public static function getBreadcrumb(): string
    {
        return static::$pluralModelLabel;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Complete the category details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Category Name')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Enter the category name (maximum 255 characters)')
                            ->placeholder('Ex. Electronics, Clothing, Home')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'The category name is required.',
                                'max' => 'The name must not exceed 255 characters.',
                                'unique' => 'This category already exists.',
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Category $record) => 'Created on ' . $record->created_at->format('d/m/Y'))
                    ->weight('medium')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('recent')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subMonth()))
                    ->label('Recent (last month)'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('success')
                    ->tooltip('Edit category'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Category')
                    ->modalDescription('Are you sure you want to delete this category? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->tooltip('Delete category'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete selected')
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected categories')
                        ->modalDescription('Are you sure you want to delete the selected categories? This action cannot be undone.'),
                ]),
            ])
            ->emptyStateHeading('No categories yet')
            ->emptyStateDescription('Create your first category by clicking the button above')
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Category')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

