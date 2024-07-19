<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
//de Filament
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Container\BindingResolutionException;
use Filament\Tables\Columns\TextColumn;


//models
use App\Models\Area;

//pluggins
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;
    protected static ?string $modelLabel = 'inventario';
    protected static ?string $pluralModelLabel = 'inventarios';
    protected static ?string $navigationIcon = '';
    protected static ?string $navigationLabel = 'STOCK';
    protected static ?string $navigationGroup = 'Inventarios';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\Select::make('IdArea')
                    ->label('Área')
                    ->relationship('areas','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('IdProduct')
                    ->label('Producto')
                    ->relationship('products','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('IsActive')
                    ->label('Activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('IsActive')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('areas.Name')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products.Name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),                            
                Tables\Columns\TextColumn::make('Quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('EstimatedDurability')
                    ->label('Durabilidad Estimada')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), 
                Tables\Columns\TextColumn::make('products.LeadTime')
                    ->label('Tiempo de entrega')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pedir' => 'info',
                        'Se agotará' => 'warning',
                        'Disponible' => 'success',
                        'Sin stock' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
                SelectFilter::make('IdArea')
                    ->label('Área')
                    ->multiple()
                    ->options(
                        fn (): array =>
                        Area::all()->pluck('Name', 'id')->all()
                    )
                    ->searchable()
                    ->default(null),
                SelectFilter::make('Status')
                    ->label('Estado')
                    ->options([
                        'Sin stock' => 'Sin stock',
                        'Se agotará' => 'Se agotará',
                        'Pedir' => 'Pedir',
                        'Disponible' => 'Disponible',
                    ])
                    ->default(null),

                TernaryFilter::make('IsActive')
                    ->label('Activo')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Productos activos')
                    ->falseLabel('Productos inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Exportar a Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary'),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
