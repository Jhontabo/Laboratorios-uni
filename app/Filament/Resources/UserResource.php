<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Roles y Permisos';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('maximo 255 caracteres'),
                TextInput::make('apellido')
                    ->required()
                    ->maxLength(255)
                    ->helperText('maximo 255 caracteres'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->helperText('maximo 255 caracteres'),
                TextInput::make('direccion')
                    ->required()
                    ->maxLength(255)
                    ->helperText('maximo 255 caracteres'),
                TextInput::make('telefono')
                    ->tel()
                    ->required()
                    ->maxLength(15)
                    ->helperText('maximo 15 caracteres'),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo')
                    ->required(),

                    
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('apellido')->label('Apellido')->sortable()->searchable(),
                TextColumn::make('email')->label('Correo')->sortable()->searchable(),
                TextColumn::make('roles')
                    ->label('Rol')
                    ->formatStateUsing(fn($state, $record) => $record->roles->pluck('name')->join(', '))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(function ($state) {
                        return $state === 'activo' ? 'success' : 'danger';
                    }),

            ])
            ->filters([
                Filter::make('estado')
                    ->label('Estado')
                    ->form([
                        Select::make('estado')
                            ->options([
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo',
                            ])
                            ->placeholder('Todos'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['estado'], fn($q) => $q->where('estado', $data['estado']))),

                Filter::make('name')
                    ->form([
                        TextInput::make('name')->label('Nombre')->placeholder('Buscar por nombre'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['name'], fn($q) => $q->where('name', 'like', "%{$data['name']}%"))),
            ])

            // acciones que se puede realizar en la tabla

            ->actions([
                Action::make('toggleEstado')
                    ->label(fn(Model $record) => $record->estado === 'activo' ? 'Desactivar' : 'Activar')
                    ->action(function (Model $record) {
                        $record->estado = $record->estado === 'activo' ? 'inactivo' : 'activo';
                        $record->save();

                        Notification::make()
                            ->title($record->estado === 'activo' ? 'Usuario Activado' : 'Usuario Desactivado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color(fn(Model $record) => $record->estado === 'activo' ? 'danger' : 'success')
                    ->icon(fn(Model $record) => $record->estado === 'activo' ? 'jam-user-remove' : 'sui-user-add'),
                // Otras acciones...
            ])


            ->bulkActions([

                Tables\Actions\BulkActionGroup::make(
                    [
                        ExportBulkAction::make()->exports([
                            ExcelExport::make('table')->fromTable()
                                ->withFilename('Timesheet_' . date('d-m-Y') . '_export')
                                ->askForWriterType(),
                        ]),




                        BulkAction::make('activarSeleccionados')
                            ->label('Activar Seleccionados')
                            ->action(function (Collection $records) {
                                $records->each(function (Model $record) {
                                    $record->estado = 'activo';
                                    $record->save();
                                });

                                Notification::make()
                                    ->title('Usuarios Activados')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation()
                            ->color('success')
                            ->icon('sui-user-add'),

                        BulkAction::make('desactivarSeleccionados')
                            ->label('Desactivar Seleccionados')
                            ->action(function (Collection $records) {
                                $records->each(function (Model $record) {
                                    if (!in_array($record->roles->pluck('name')->first(), ['ADMIN', 'LABORATORISTA'])) {
                                        $record->estado = 'inactivo';
                                        $record->save();
                                    }
                                });

                                Notification::make()
                                    ->title('Usuarios Desactivados')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation()
                            ->color('danger')
                            ->icon('jam-user-remove'),
                    ]
                ),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
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
