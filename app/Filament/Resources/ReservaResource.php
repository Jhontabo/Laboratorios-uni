<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Filament\Resources\ReservaResource\RelationManagers;
use App\Models\Reserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_usuario')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_laboratorio')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_horario')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_laboratorista')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('Estado')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_usuario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_laboratorio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_horario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_laboratorista')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListReservas::route('/'),
            'create' => Pages\CreateReserva::route('/create'),
            'edit' => Pages\EditReserva::route('/{record}/edit'),
        ];
    }
}
