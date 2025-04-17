<?php

namespace App\Filament\Resources\GameCredentialResource\Pages;

use App\Filament\Resources\GameCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameCredential extends EditRecord
{
    protected static string $resource = GameCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
