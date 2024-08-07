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
    protected static ?string $modelLabel = 'área';
    protected static ?string $pluralModelLabel = 'áreas';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'ÁREA';
    protected static ?string $navigationGroup = 'Inventarios';


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
                ->label('Identificación')
                ->required()
                ->maxLength(255)
                ->placeholder('Ingrese Nº Identificación área'),

                Forms\Components\TextInput::make('Name')
                    ->label('Nombre')
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
                    ->label('Identificación')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha modificación')
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
