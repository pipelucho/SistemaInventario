<?php

namespace App\Filament\Resources\OutputorderResource\Pages;

use App\Filament\Resources\OutputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutputorder extends EditRecord
{
    protected static string $resource = OutputorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
