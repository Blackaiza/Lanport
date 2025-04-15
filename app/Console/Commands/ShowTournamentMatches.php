<?php

namespace App\Console\Commands;

use App\Models\TournamentMatch;
use Illuminate\Console\Command;

class ShowTournamentMatches extends Command
{
    protected $signature = 'tournament:show-matches {competition_id}';
    protected $description = 'Show tournament matches with their details';

    public function handle()
    {
        $competitionId = $this->argument('competition_id');

        $matches = TournamentMatch::where('competition_id', $competitionId)
            ->orderBy('round')
            ->orderBy('match_number')
            ->get();

        $this->info("Tournament Matches for Competition ID: {$competitionId}");
        $this->info("=========================================");

        foreach ($matches as $match) {
            $this->info("\nMatch ID: {$match->id}");
            $this->info("Round: {$match->round}");
            $this->info("Bracket: {$match->bracket}");
            $this->info("Match Number: {$match->match_number}");
            $this->info("Team 1: " . ($match->team1 ? $match->team1->team_name : 'TBD'));
            $this->info("Team 2: " . ($match->team2 ? $match->team2->team_name : 'TBD'));
            $this->info("Winner: " . ($match->winner ? $match->winner->team_name : 'TBD'));
            $this->info("-----------------------------------------");
        }
    }
}
