<?php

namespace App\Console\Commands;

use App\Models\TournamentMatch;
use Illuminate\Console\Command;

class FixTournamentRounds extends Command
{
    protected $signature = 'tournament:fix-rounds {competition_id}';
    protected $description = 'Fix round names for tournament matches';

    public function handle()
    {
        $competitionId = $this->argument('competition_id');

        // Update round names
        TournamentMatch::where('competition_id', $competitionId)
            ->where('round', 'semi_finals')
            ->update(['round' => 'upper_semi_finals']);

        TournamentMatch::where('competition_id', $competitionId)
            ->where('round', 'finals')
            ->update(['round' => 'upper_finals']);

        $this->info('Round names have been updated successfully.');
    }
}
