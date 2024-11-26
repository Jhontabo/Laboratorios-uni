<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\select;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Roles y Permisos';
    protected static ?string $navigationLabel = 'Usuarios';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('apellido')
                    ->required()
                    ->maxLength(255),
                TextInput::make('correo_electronico')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('Direccion')
                    ->required()
                    ->maxLength(255),
                TextInput::make('telefono')
                    ->tel()
                    ->required()
                    ->maxLength(15),
                Select::make('roles')->multiple()->relationship('roles', 'name'),
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
                TextColumn::make('nombre')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('apellido')->label('Apellido')->sortable()->searchable(),
                TextColumn::make('correo_electronico')->label('Correo')->sortable()->searchable(),
                TextColumn::make('roles.name')  // Mostrar roles de los usuarios
                    ->label('Roles')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return is_array($state) ? implode(', ', $state) : $state;
                    }),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucfirst($state))  // Capitaliza el estado
                    ->color(function ($state) {
                        return $state === 'activo' ? 'success' : 'danger';
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
