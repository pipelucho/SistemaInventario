<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
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
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

//pluggins
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

//models
use App\Models\Area;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $modelLabel = 'cotización';
    protected static ?string $pluralModelLabel = 'cotizaciones';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'COTIZACIÓN';
    protected static ?string $navigationGroup = 'Pedidos';

    


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /*
                Forms\Components\DateTimePicker::make('CreatedDate')
                    ->required(),*/
                Forms\Components\Select::make('IdArea')
                    ->label('Área')
                    ->relationship('areas','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('Quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('IdProduct')
                    ->label('Producto')
                    ->relationship('products','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                    /*
                Forms\Components\TextInput::make('Description')
                    ->required()
                    ->maxLength(510),*/
                Forms\Components\DateTimePicker::make('RequieredDate')
                    ->label('Fecha Requerida')
                    ->required(),
                Forms\Components\Select::make('IdSupplier')
                    ->label('Proveedor')
                    ->relationship('suppliers','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areas.Name')
                    ->label('Áreas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('products.Name')
                    ->label('Productos')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Description')
                    ->label('Descripción')
                    ->label('Descripción Producto Pedido')
                    ->searchable()
                    ->color('success')
                    ->copyable()
                    ->copyMessage('Copiado al portapapeles.')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('Quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Copiado al portapapeles.')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->searchable()->copyable()
                    ->copyMessage('Copiado al portapapeles.')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('suppliers.Name')
                    ->label('Proveedor')
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Copiado al portapapeles.')
                    ->copyMessageDuration(1500),
                
                Tables\Columns\TextColumn::make('RequieredDate')
                    ->label('Fecha Requerida')
                    ->dateTime()
                    ->sortable(),

                

                Tables\Columns\TextColumn::make('CreatedDate')
                    ->label('Fecha Cotización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
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
                Filter::make('created_at')
                    ->label('Fecha de creación')
                    ->form([
                        DatePicker::make('created_from')->label('Recibido Desde'),
                        DatePicker::make('created_until')->label('Recibido Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('CreatedDate', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('CreatedDate', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
