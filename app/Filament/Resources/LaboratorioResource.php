<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaboratorioResource\Pages;
use App\Filament\Resources\LaboratorioResource\RelationManagers;
use App\Models\Laboratorio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use  Filament\Forms\Components\TextInput;

class LaboratorioResource extends Resource
{
    protected static ?string $model = Laboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'laboratorios';

    protected static ?string $navigationGroup = 'Laboratorios';
    

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Maximo 255 caracteres'),
                TextInput::make('ubicacion')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Maximo 255 caracteres'),
                TextInput::make('capacidad')
                    ->numeric()
                    ->required()
                    ->rules('gt:0|max:100')
                    ->helperText('minimo 1'),

                Select::make('id_usuario')
                    ->label('Laboratorista')
                    ->options(User::role('LABORATORISTA')->get()->pluck('name', 'id_usuario'))
                    ->preload()
                    ->required()
                    ->helperText('Seleccione el laboratorista responsable del laboratorio'),



            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre'),
                TextColumn::make('ubicacion'),
                TextColumn::make('capacidad'),
                TextColumn::make('laboratorista.name')
                    ->label('Laboratorista Responsable')
                    ->sortable()
                    ->searchable(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
