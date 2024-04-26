<?php

namespace App\Filament\Resources\InputorderResource\Pages;

use App\Filament\Resources\InputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInputorder extends CreateRecord
{
    protected static string $resource = InputorderResource::class;

    //funcion para que al crear redirija a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
