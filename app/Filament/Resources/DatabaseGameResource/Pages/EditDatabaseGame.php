<?php

namespace App\Filament\Resources\DatabaseGameResource\Pages;

use App\Filament\Resources\DatabaseGameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDatabaseGame extends EditRecord
{
    protected static string $resource = DatabaseGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
