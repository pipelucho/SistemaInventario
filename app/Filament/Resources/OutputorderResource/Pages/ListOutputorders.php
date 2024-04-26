<?php

namespace App\Filament\Resources\OutputorderResource\Pages;

use App\Filament\Resources\OutputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutputorders extends ListRecords
{
    protected static string $resource = OutputorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
