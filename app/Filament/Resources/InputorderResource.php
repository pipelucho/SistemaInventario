<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InputorderResource\Pages;
use App\Filament\Resources\InputorderResource\RelationManagers;
use App\Models\Inputorder;
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
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

//models
use App\Models\Area;

//pluggins
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class InputorderResource extends Resource
{
    protected static ?string $model = Inputorder::class;

    protected static ?string $modelLabel = 'entrada';
    protected static ?string $pluralModelLabel = 'entradas';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static ?string $navigationLabel = 'ENTRADA';
    protected static ?string $navigationGroup = 'Pedidos';



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
                Forms\Components\TextInput::make('Quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('Description')
                    ->label('Descripción')
                    //->required()
                    ->maxLength(255),
                Forms\Components\Select::make('IdProduct')
                    ->label('Producto')
                    ->relationship('products','Name')
                    //->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('IdSupplier')
                    ->label('Proveedor')
                    ->relationship('suppliers','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('OrderDate')
                    ->label('Fecha de Pedido')
                    ->required(),
                Forms\Components\Select::make('IdQuote')
                    ->label('Cotización')
                    ->relationship('quotes','Description')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('Status')
                    ->label('Estado')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('Status')
                    ->label('Estado')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('areas.Name')
                    ->label('Área')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('Quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Description')
                    ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('products.Name')
                    ->label('Producto')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suppliers.Name')
                    ->label('Proveedor')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('QuoteDate')
                    ->label('Fecha de cotización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('OrderDate')
                    ->label('Fecha de Pedido')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ReceivedDate')
                    ->label('Fecha Recibido')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('RequieredDate')
                    ->label('Fecha Requerido')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Fk_DocumentNoBC')
                    ->label('Nº Documento')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Fk_LineNoBC')
                    ->label('Nº Linea')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
            ])->defaultSort('id', 'desc')
            ->filters([
                //
                SelectFilter::make('Status')
                    ->label('Estado')
                    ->options([
                        1 => 'True',  // 1 representa true en booleano
                        0 => 'False'  // 0 representa false en booleano
                    ])
                    ->default(null),
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
                    ->label('Fecha creación')
                    ->form([
                        DatePicker::make('created_from')->label('Recibido Desde'),
                        DatePicker::make('created_until')->label('Recibido Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ReceivedDate', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ReceivedDate', '<=', $date),
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
            'index' => Pages\ListInputorders::route('/'),
            'create' => Pages\CreateInputorder::route('/create'),
            //'edit' => Pages\EditInputorder::route('/{record}/edit'),
            'edit' => Pages\EditInputorder::route('/{record}/edit'),
        ];
    }
}
