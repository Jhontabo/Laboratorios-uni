<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanManagementResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LoanManagementResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Loan Management';
    protected static ?string $modelLabel = 'Loan Management';
    protected static ?string $navigationGroup = 'Loans';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'product:id,name,image,available_quantity',
                'user:id,name,last_name,email',
            ])
            ->select([
                'id',
                'product_id',
                'user_id',
                'status',
                'request_date',
                'approval_date',
                'estimated_return_date',
                'real_return_date',
            ])
            ->whereIn('status', ['pending', 'approved', 'returned'])
            ->whereNotNull('user_id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns)
            ->columns([
                ImageColumn::make('product.image')
                    ->label('Image')
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.name')
                    ->label('Equipment')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.available_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Tables\Columns\ColumnGroup::make('User')
                    ->columns([
                        TextColumn::make('user.name')
                            ->label('Name')
                            ->formatStateUsing(fn($state, $record) => "{$record->user->name} {$record->user->last_name}")
                            ->searchable(query: function (Builder $query, string $search): Builder {
                                return $query->whereHas('user', function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%");
                                });
                            }),

                        TextColumn::make('user.email')
                            ->label('Email')
                            ->searchable(),
                    ]),

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

                TextColumn::make('request_date')
                    ->label('Request')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('approval_date')
                    ->label('Approval')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Not approved')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('estimated_return_date')
                    ->label('Estimated Return')
                    ->dateTime('d M Y')
                    ->color(fn($record) =>
                        $record->status === 'approved' && $record->estimated_return_date < now()
                            ? 'danger'
                            : 'success'
                    )
                    ->sortable()
                    ->placeholder('Not assigned')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('real_return_date')
                    ->label('Returned')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Not returned'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('estimated_return_date')
                            ->label('Estimated Return Date')
                            ->required()
                            ->minDate(now()->addDay())
                            ->default(now()->addWeek())
                            ->displayFormat('d M Y')
                    ])
                    ->action(function (Loan $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $product = $record->product;
                            $estimatedReturnDate = \Carbon\Carbon::parse($data['estimated_return_date']);

                            if ($product->available_quantity <= 0) {
                                throw new \Exception('No available units');
                            }

                            $record->update([
                                'status' => 'approved',
                                'approval_date' => now(),
                                'estimated_return_date' => $estimatedReturnDate,
                            ]);

                            $product->decrement('available_quantity');
                            $product->update([
                                'available_for_loan' => $product->available_quantity >= 5,
                            ]);

                            $message = $product->available_quantity < 5
                                ? "Product marked as unavailable. Current stock: {$product->available_quantity}"
                                : "Deadline: " . $estimatedReturnDate->format('d/m/Y');

                            Notification::make()
                                ->success()
                                ->title('Loan approved')
                                ->body($message)
                                ->send();
                        });
                    })
                    ->visible(fn(Loan $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Loan')
                    ->modalDescription('Are you sure you want to reject this loan?')
                    ->action(function (Loan $record) {
                        $record->update([
                            'status' => 'rejected',
                            'estimated_return_date' => null,
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Loan rejected')
                            ->send();
                    })
                    ->visible(fn(Loan $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('return')
                    ->label('Mark as Returned')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DatePicker::make('real_return_date')
                            ->label('Actual Return Date')
                            ->default(now())
                            ->maxDate(now())
                            ->displayFormat('d M Y')
                    ])
                    ->action(function (Loan $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $realReturnDate = \Carbon\Carbon::parse($data['real_return_date']);
                            $product = $record->product;

                            $record->update([
                                'status' => 'returned',
                                'real_return_date' => $realReturnDate,
                            ]);

                            $product->increment('available_quantity');
                            $product->update([
                                'available_for_loan' => true,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Equipment returned')
                                ->body("Return date: {$realReturnDate->format('d/m/Y')}")
                                ->send();
                        });
                    })
                    ->visible(fn(Loan $record) => $record->status === 'approved'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve')
                    ->label('Approve selected')
                    ->icon('heroicon-o-check')
                    ->form([
                        Forms\Components\DatePicker::make('estimated_return_date')
                            ->label('Estimated Return Date')
                            ->required()
                            ->minDate(now()->addDay())
                            ->default(now()->addWeek())
                    ])
                    ->action(function ($records, array $data) {
                        $estimatedReturnDate = \Carbon\Carbon::parse($data['estimated_return_date']);

                        $records->each->update([
                            'status' => 'approved',
                            'approval_date' => now(),
                            'estimated_return_date' => $estimatedReturnDate,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Loans approved')
                            ->body("Deadline: {$estimatedReturnDate->format('d/m/Y')}")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanManagements::route('/'),
        ];
    }
}

