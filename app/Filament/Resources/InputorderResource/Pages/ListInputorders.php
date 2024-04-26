<?php

namespace App\Filament\Resources\InputorderResource\Pages;

use App\Filament\Resources\InputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInputorders extends ListRecords
{
    protected static string $resource = InputorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
