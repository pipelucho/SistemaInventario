<?php

namespace App\Filament\Resources\InputorderResource\Pages;

use App\Filament\Resources\InputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInputorder extends EditRecord
{
    protected static string $resource = InputorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
