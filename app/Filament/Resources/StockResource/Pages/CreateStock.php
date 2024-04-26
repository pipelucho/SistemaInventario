<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;

    //funcion para que al crear redirija a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
