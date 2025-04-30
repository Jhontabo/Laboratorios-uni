<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvailableProductResource\Pages;
use App\Models\Loan;
use App\Models\AvailableProduct;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;

class AvailableProductResource extends Resource
{
    protected static ?string $model = AvailableProduct::class;
    protected static ?string $navigationIcon = 'heroicon-m-shopping-cart';
    protected static ?string $navigationGroup = 'Loans';
    protected static ?string $title = 'Available Products';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('available_for_loan', true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->circular()
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => substr($record->description, 0, 50) . '...')
                    ->weight('medium')
                    ->color('primary'),

                TextColumn::make('available_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($record) => $record->available_quantity > 10 ? 'success' : ($record->available_quantity > 0 ? 'warning' : 'danger'))
                    ->icon(fn($record) => $record->available_quantity > 10 ? 'heroicon-o-check-circle' : ($record->available_quantity > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-x-circle')),

                TextColumn::make('product_condition')
                    ->label('Condition')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'new' => 'success',
                        'used' => 'warning',
                        'damaged' => 'danger',
                        'discarded' => 'gray',
                        'lost' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('product_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'equipment' => 'info',
                        'supply' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('laboratory.location')
                    ->label('Location')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('infoSelection')
                    ->label('How to request products?')
                    ->color('gray')
                    ->icon('heroicon-o-question-mark-circle')
                    ->modalContent(view('filament.pages.instructions-request'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Understood'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('requestLoan')
                    ->label('Request Products (max. 5)')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->action(function ($records) {
                        if ($records->count() > 5) {
                            Notification::make()
                                ->title('Limit Exceeded')
                                ->body('You can only select up to 5 products')
                                ->danger()
                                ->send();
                            return;
                        }

                        foreach ($records as $record) {
                            if ($record->available_quantity <= 0) {
                                Notification::make()
                                    ->title("{$record->name} not available")
                                    ->danger()
                                    ->send();
                                continue;
                            }

                            Loan::create([
                                'product_id' => $record->id,
                                'user_id' => auth()->id(),
                                'status' => 'pending',
                                'requested_at' => now(), // Change this from 'request_date' to 'requested_at'
                            ]);
                        }

                        Notification::make()
                            ->title('Request registered')
                            ->body("{$records->count()} products requested successfully")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Request')
                    ->modalDescription(fn($records) => "Do you confirm the request of {$records->count()} products?")
                    ->deselectRecordsAfterCompletion(),
            ])
            ->emptyState(view('filament.pages.empty-state-products'))
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvailableProducts::route('/'),
            'view' => Pages\ViewAvalibleProducts::route('/{record}'),
        ];
    }
}
