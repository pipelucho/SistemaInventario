<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArea extends CreateRecord
{
    protected static string $resource = AreaResource::class;

    //funcion para que al crear redirija a la tabla principal
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
