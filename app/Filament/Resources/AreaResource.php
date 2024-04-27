<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Filament\Resources\AreaResource\RelationManagers;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Facades\Auth;
use App\Models\User;





class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

/*
    public static function form(Form $form): Form
    {
        $IdUser = Auth::id(); // Obtener el ID del usuario autenticado

        return $form
            ->schema([
                Forms\Components\TextInput::make('Name')
                    ->required()
                    ->maxLength(510),
                Forms\Components\Hidden::make('IdUser') // Campo oculto para el ID del usuario
                    ->value($IdUser) // Asignar el ID del usuario actual
            ]);
    }*/
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $userId = $user ? $user->getFilamentIdUser() : null;
        
        return $form
            ->schema([
                Forms\Components\TextInput::make('Identification')
                ->required()
                ->maxLength(255)
                ->placeholder('Ingrese Nº Identificacion área'),

                Forms\Components\TextInput::make('Name')
                    ->required()
                    ->maxLength(510)
                    ->placeholder('Ingrese Nombre del área'),
                /*
                Forms\Components\Hidden::make('UserName') // Campo oculto para el nombre del usuario
                    ->defaultView($userId) */// Establecer el valor predeterminado del campo
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Name')
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
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
