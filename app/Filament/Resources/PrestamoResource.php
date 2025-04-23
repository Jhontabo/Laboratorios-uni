<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestamoResource\Pages;
use App\Models\Prestamo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Mis Préstamos';
    protected static ?string $navigationGroup = 'Prestamos';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['producto']) // Solo necesitamos la relación con producto
            ->where('user_id', Auth::id()) // Filtra solo los préstamos del usuario actual
            ->orderBy('created_at', 'desc'); // Ordena por fecha más reciente
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('producto_id')
                    ->relationship('producto', 'nombre')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->required()
                    ->disabled(),
                Forms\Components\DateTimePicker::make('fecha_solicitud')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('fecha_aprobacion')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('fecha_devolucion_estimada')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('fecha_devolucion_real')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('producto.imagen')
                    ->label('Imagen')
                    ->size(50),

                TextColumn::make('producto.nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado' => 'success',
                        'rechazado' => 'danger',
                        'devuelto' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'devuelto' => 'Devuelto',
                        default => $state,
                    }),

                TextColumn::make('fecha_solicitud')
                    ->label('Solicitud')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('fecha_aprobacion')
                    ->label('Aprobación')
                    ->dateTime('d M Y H:i')
                    ->placeholder('No aprobado')
                    ->sortable(),

                TextColumn::make('fecha_devolucion_estimada')
                    ->label('Devolución Estimada')
                    ->dateTime('d M Y')
                    ->placeholder('No asignada'),

                TextColumn::make('fecha_devolucion_real')
                    ->label('Devuelto')
                    ->dateTime('d M Y H:i')
                    ->placeholder('No devuelto'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->label('Estado del préstamo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]) // Eliminamos las bulk actions ya que el usuario no debe realizar acciones masivas
            ->emptyStateHeading('No tienes préstamos registrados');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestamos::route('/'),
            //'view' => Pages\ViewPrestamo::route('/{record}'),
        ];
    }

    // Opcional: Para ocultar el recurso si el usuario no tiene permisos
    public static function canViewAny(): bool
    {
        return Auth::check(); // Solo visible para usuarios autenticados
    }
}