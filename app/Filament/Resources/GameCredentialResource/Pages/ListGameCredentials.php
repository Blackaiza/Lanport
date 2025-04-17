<?php

namespace App\Filament\Resources\GameCredentialResource\Pages;

use App\Filament\Resources\GameCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameCredentials extends ListRecords
{
    protected static string $resource = GameCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}