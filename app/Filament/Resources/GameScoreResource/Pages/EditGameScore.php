<?php

namespace App\Filament\Resources\GameScoreResource\Pages;

use App\Filament\Resources\GameScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameScore extends EditRecord
{
    protected static string $resource = GameScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}