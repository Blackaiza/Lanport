<?php

namespace App\Filament\Resources\DatabaseGameResource\Pages;

use App\Filament\Resources\DatabaseGameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDatabaseGames extends ListRecords
{
    protected static string $resource = DatabaseGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
