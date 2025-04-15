<?php

namespace App\Filament\Resources\TeamCompetitionApproveResource\Pages;

use App\Filament\Resources\TeamCompetitionApproveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamCompetitionApproves extends ListRecords
{
    protected static string $resource = TeamCompetitionApproveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove create action as approvals should only come from registrations
        ];
    }
}
