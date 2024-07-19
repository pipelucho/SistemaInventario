<?php

namespace App\Filament\Widgets;

use Livewire\Component; // Asegúrate de importar Livewire
use Filament\Forms\Components\Section; // Importa la clase Section

trait InteractsWithPageFilters
{
    public array $filters = [];

    public function updatedFilters()
    {
        $this->emit('updateChart');
    }

    
}