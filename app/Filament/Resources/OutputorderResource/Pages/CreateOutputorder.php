<?php

namespace App\Filament\Resources\OutputorderResource\Pages;

use App\Filament\Resources\OutputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOutputorder extends CreateRecord
{
    protected static string $resource = OutputorderResource::class;

    //funcion para que al crear redirija a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
