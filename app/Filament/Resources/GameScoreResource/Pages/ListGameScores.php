<?php

namespace App\Filament\Resources\GameScoreResource\Pages;

use App\Filament\Resources\GameScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameScores extends ListRecords
{
    protected static string $resource = GameScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
