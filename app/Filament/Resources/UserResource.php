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
                Select::make('roles')->multiple()->relationship('roles', 'name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('apellido')->label('Apellido')->sortable()->searchable(),
                TextColumn::make('correo_electronico')->label('Correo')->sortable()->searchable(),
                TextColumn::make('roles.name')  // Usamos TextColumn para mostrar los nombres de los roles
                    ->label('Roles')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Concatenar los roles si son múltiples
                        return is_array($state) ? implode(', ', $state) : $state;
                    })
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
