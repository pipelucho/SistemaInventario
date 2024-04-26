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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('IdArea')
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
                    ->required()
                    ->numeric()
                    ->rules('required', 'numeric', new CheckStockQuantity),                  
                Forms\Components\TextInput::make('UnitMeasurement')
                    ->maxLength(255),
                Forms\Components\Select::make('IdProduct')
                    ->relationship('products','Name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('IdEmployee')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('products.Name')
                    ->searchable()
                    ->sortable(),
                    /*
                Tables\Columns\TextColumn::make('CreatedDate')
                    ->dateTime()
                    ->sortable(),*/
                
                Tables\Columns\TextColumn::make('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('UnitMeasurement')
                    ->searchable(),
                Tables\Columns\TextColumn::make('EstimatedDurability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employees.Name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreatedDate')
                    ->dateTime()
                    ->sortable(),                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
