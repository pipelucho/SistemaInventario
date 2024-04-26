<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    //funcion para que al crear redirija a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
