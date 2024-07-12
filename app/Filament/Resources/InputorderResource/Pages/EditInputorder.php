<?php

namespace App\Filament\Resources\InputorderResource\Pages;

use App\Filament\Resources\InputorderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Notifications\Notification;
use App\Models\Stock;

class EditInputorder extends EditRecord
{
    protected static string $resource = InputorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if Status is true
        if ($data['Status'] === true) {
            // Check if a record in stocks with matching IdArea and IdProduct exists
            $stockExists = Stock::where('IdProduct', $data['IdProduct'])
                                ->where('IdArea', $data['IdArea'])
                                ->exists();

            // If stock record does not exist, show notification and halt save
            if (!$stockExists) {
                Notification::make()
                    ->title('Error')
                    ->body('No se puede guardar un inputorder con estado True si no existe un registro en la tabla stocks con las condiciones dadas.')
                    ->danger()
                    ->send();

                // Return current data without saving
                return $this->record->toArray();
            }
        }

        return $data;
    }

    protected function beforeSave()
    {
        $data = $this->data;

        // Check if Status is true
        if ($data['Status'] === true) {
            // Check if a record in stocks with matching IdArea and IdProduct exists
            $stockExists = Stock::where('IdProduct', $data['IdProduct'])
                                ->where('IdArea', $data['IdArea'])
                                ->exists();

            // If stock record does not exist, show notification and halt save
            if (!$stockExists) {
                Notification::make()
                    ->title('Error')
                    ->body('No se puede guardar un inputorder con estado True si no existe un registro en la tabla stocks con las condiciones dadas.')
                    ->danger()
                    ->send();

                // Halt the save process
                $this->halt();

                return;
            }
        }
    }
}