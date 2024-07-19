<?php


namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\Toggle;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

use Filament\Forms\Components\Section; // Importa la clase Section

//models
use App\Models\Area;
use App\Models\Product;


use Livewire\Component; // Asegúrate de importar Livewire


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('')->schema([
                Select::make('IdProduct')
                ->options(Product::all()->pluck('Name', 'id'))
                ->searchable()
                ->preload()
                ->label('Producto'),
                
                Select::make('IdArea')
                    ->options(Area::all()->pluck('Name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Área'),
                DatePicker::make('startDate')->label('Desde'),
                DatePicker::make('endDate')->label('Hasta'),
                //Toggle::make('active'),
            ])->columns(4)
            
        ]);
    }

    
}
