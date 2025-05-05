<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
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
    protected static ?string $navigationLabel = 'Mis prestamos';
    protected static ?string $navigationGroup = 'Prestamos';
    protected static ?string $modelLabel = 'Prestamo';
    protected static ?string $pluralModelLabel = 'Mis prestamos';
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
                    ->label('Imagen')
                    ->size(50),

                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
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
                    ->label('Fecha peticion')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label('Fecha aprobado')
                    ->dateTime('d M Y H:i')
                    ->placeholder('No aprobado')
                    ->sortable(),

                TextColumn::make('estimated_return_at')
                    ->label('Fecha devolucion')
                    ->dateTime('d M Y')
                    ->placeholder('No Asigando'),

                TextColumn::make('actual_return_at')
                    ->label('Devuelto')
                    ->dateTime('d M Y H:i')
                    ->placeholder('No Devuelto'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'returned' => 'Returned',
                    ])
                    ->label('Estado del prestamo'),
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
