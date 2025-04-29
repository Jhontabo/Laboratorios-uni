<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'My Loans';
    protected static ?string $navigationGroup = 'Loans';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['product'])
            ->where('user_id', Auth::id());
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.image')
                    ->label('Image')
                    ->size(50),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'returned' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                TextColumn::make('requested_at')
                    ->label('Request Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label('Approval Date')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Not approved')
                    ->sortable(),

                TextColumn::make('estimated_return_at')
                    ->label('Estimated Return')
                    ->dateTime('d M Y')
                    ->placeholder('Not assigned'),

                TextColumn::make('actual_return_at')
                    ->label('Returned')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Not returned'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'returned' => 'Returned',
                    ])
                    ->label('Loan Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]) // No bulk actions needed
            ->emptyStateHeading('You have no loans yet');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check();
    }
}

