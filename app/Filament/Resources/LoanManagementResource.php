<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanManagementResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanManagementResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Administrar Prestamos';
    protected static ?string $navigationGroup = 'Prestamos';
    protected static ?string $modelLabel = 'Prestamo';
    protected static ?string $pluralModelLabel = 'Prestamos';


    public static function canViewAny(): bool
    {
        $user = auth()->user();
        // Lógica personalizada
        return $user && $user->hasRole('ADMIN') || $user->hasRole('LABORATORISTA');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }

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
                'requested_at', // Changed from 'request_date' to 'requested_at'
                'approved_at',  // Changed from 'approval_date' to 'approved_at'
                'estimated_return_at',
                'actual_return_at', // Changed from 'real_return_date' to 'actual_return_at'
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
                    ->label('Imagen')
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.name')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.available_quantity')
                    ->label('Cantidad dsiponible')
                    ->sortable()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Tables\Columns\ColumnGroup::make('User')
                    ->label('Usuario')
                    ->columns([
                        TextColumn::make('user.name')
                            ->label('Nombre')
                            ->formatStateUsing(fn($state, $record) => "{$record->user->name} {$record->user->last_name}")
                            ->searchable(query: function (Builder $query, string $search): Builder {
                                return $query->whereHas('user', function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%");
                                });
                            }),

                        TextColumn::make('user.email')
                            ->label('Corre electronico')
                            ->searchable(),
                    ]),

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

                TextColumn::make('requested_at') // Changed from 'request_date' to 'requested_at'
                    ->label('fecha de peticion')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('approved_at') // Changed from 'approval_date' to 'approved_at'
                    ->label('Aprobado')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('No aprobado')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('estimated_return_at')
                    ->label('Fecha devolucion')
                    ->dateTime('d M Y')
                    ->color(
                        fn($record) =>
                        $record->status === 'approved' && $record->estimated_return_at < now()
                            ? 'danger'
                            : 'success'
                    )
                    ->sortable()
                    ->placeholder('No Asignado')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('actual_return_at') // Changed from 'real_return_date' to 'actual_return_at'
                    ->label('Devuelto')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Sin devolver'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprovado')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('estimated_return_at')
                            ->label('Fecha estimada devolucion')
                            ->required()
                            ->minDate(now()->addDay())  // Set min date to the next day
                            ->default(now()->addWeek())  // Set default to one week from now
                            ->displayFormat('d M Y')
                            ->disabled()  // Prevent modifying the approval date
                    ])
                    ->action(function (Loan $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $product = $record->product;

                            // If you want to set 'estimated_return_at' directly and not use $data from the form
                            $estimatedReturnDate = \Carbon\Carbon::parse(now()->addWeek());  // Automatically use the default date (one week from now)

                            if ($product->available_quantity <= 0) {
                                throw new \Exception('Sin unidades disponibles');
                            }

                            // Set the current date for 'approved_at' and lock it
                            $record->update([
                                'status' => 'approved',
                                'approved_at' => now(),  // Ensure 'approved_at' is the current date and time
                                'estimated_return_at' => $estimatedReturnDate, // Set estimated return date to one week from now
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
                                ->title('Prestamo aprovado')
                                ->body($message)
                                ->send();
                        });
                    })
                    ->visible(fn(Loan $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Loan')
                    ->modalDescription('Estas seguro de rechazar el prestamos?')
                    ->action(function (Loan $record) {
                        $record->update([
                            'status' => 'rejected',
                            'estimated_return_at' => null,
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Prestamos Rechazado')
                            ->send();
                    })
                    ->visible(fn(Loan $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('return')
                    ->label('Marcar como devuelto')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DatePicker::make('actual_return_at') // Changed from 'real_return_date' to 'actual_return_at'
                            ->label('fecha de devolucion')
                            ->default(now())  // Set default to current date
                            ->maxDate(now()) // Limit the date to today (cannot be in the future)
                            ->displayFormat('d M Y')
                            ->disabled() // Disable editing of the actual return date
                    ])
                    ->action(function (Loan $record, array $data) {
                        DB::transaction(function () use ($record) {
                            // Set the actual return date to the current date
                            $actualReturnDate = now();  // Use the current date as the actual return date
                            $product = $record->product;

                            $record->update([
                                'status' => 'returned',
                                'actual_return_at' => $actualReturnDate, // Record the actual return date
                            ]);

                            $product->increment('available_quantity');
                            $product->update([
                                'available_for_loan' => true,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Equipo devuelto')
                                ->body("Return date: {$actualReturnDate->format('d/m/Y')}")
                                ->send();
                        });
                    })
                    ->visible(fn(Loan $record) => $record->status === 'approved'),

            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanManagements::route('/'),
        ];
    }
}
