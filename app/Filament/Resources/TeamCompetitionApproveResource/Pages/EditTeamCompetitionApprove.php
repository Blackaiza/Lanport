<?php

namespace App\Filament\Resources\TeamCompetitionApproveResource\Pages;

use App\Filament\Resources\TeamCompetitionApproveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamCompetitionApprove extends EditRecord
{
    protected static string $resource = TeamCompetitionApproveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
