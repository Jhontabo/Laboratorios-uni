<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaboratorioResource\Pages;
use App\Filament\Resources\LaboratorioResource\RelationManagers;
use App\Models\Laboratorio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaboratorioResource extends Resource
{
    protected static ?string $model = Laboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getPluralLabel(): string
    {
        return 'Laboratorios'; 
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')->required(),
                Forms\Components\TextInput::make('ubicacion')->required(),
                Forms\Components\TextInput::make('capacidad')->numeric(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre'),
                Tables\Columns\TextColumn::make('ubicacion'),
                Tables\Columns\TextColumn::make('capacidad'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaboratorios::route('/'),
            'create' => Pages\CreateLaboratorio::route('/create'),
            'edit' => Pages\EditLaboratorio::route('/{record}/edit'),
        ];
    }
}

