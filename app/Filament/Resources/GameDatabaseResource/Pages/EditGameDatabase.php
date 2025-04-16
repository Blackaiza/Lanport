<?php

namespace App\Filament\Resources\GameDatabaseResource\Pages;

use App\Filament\Resources\GameDatabaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameDatabase extends EditRecord
{
    protected static string $resource = GameDatabaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
