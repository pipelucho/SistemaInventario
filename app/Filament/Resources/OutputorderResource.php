<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutputorderResource\Pages;
use App\Filament\Resources\OutputorderResource\RelationManagers;
use App\Models\Outputorder;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\unless;

//rules
use App\Rules\CheckStockQuantity;



//models
use App\Models\Area;
use App\Models\Stock;

//pluggins
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OutputorderResource extends Resource
{
    protected static ?string $model = Outputorder::class;
    protected static ?string $modelLabel = 'salida';
    protected static ?string $pluralModelLabel = 'salidas';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';
    protected static ?string $navigationLabel = 'SALIDA';
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
                /*
                Forms\Components\DateTimePicker::make('CreatedDate')
                    ->required(),
                Forms\Components\TextInput::make('EstimatedDurability')
                    ->required()
                    ->numeric(),*/
                Forms\Components\TextInput::make('Quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->rules('required', 'numeric', new CheckStockQuantity),                  
                Forms\Components\TextInput::make('UnitMeasurement')
                    ->label('Unidad de Medida')
                    ->maxLength(255),
                Forms\Components\Select::make('IdProduct')
                    ->label('Producto')
                    ->relationship('products','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('IdEmployee')
                    ->label('Empleado')
                    ->relationship('employees','Name')
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
                    ->label('Área')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('products.Name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                    /*
                Tables\Columns\TextColumn::make('CreatedDate')
                    ->dateTime()
                    ->sortable(),*/
                
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('employees.Name')
                    ->label('Empleado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreatedDate')
                    ->label('Fecha de Pedido')
                    ->dateTime()
                    ->sortable(),                    
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
                    ->label('Fecha Creación')
                    ->form([
                        DatePicker::make('created_from')->label('Entregado Desde'),
                        DatePicker::make('created_until')->label('Entregado Hasta'),
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
            'index' => Pages\ListOutputorders::route('/'),
            'create' => Pages\CreateOutputorder::route('/create'),
            'edit' => Pages\EditOutputorder::route('/{record}/edit'),
        ];
    }
}
