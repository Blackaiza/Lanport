<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentMatch;

class FixTournamentBrackets extends Command
{
    protected $signature = 'tournament:fix-brackets {competition_id}';
    protected $description = 'Fix bracket values for tournament matches';

    public function handle()
    {
        $competitionId = $this->argument('competition_id');

        // Update upper bracket matches
        TournamentMatch::where('competition_id', $competitionId)
            ->where(function($query) {
                $query->where('round', 'like', 'upper_%')
                      ->orWhere('round', 'grand_finals');
            })
            ->update(['bracket' => 'upper']);

        // Update lower bracket matches
        TournamentMatch::where('competition_id', $competitionId)
            ->where('round', 'like', 'lower_%')
            ->update(['bracket' => 'lower']);

        $this->info('Bracket values have been updated successfully.');

        // Show the updated matches
        $matches = TournamentMatch::where('competition_id', $competitionId)
            ->orderBy('round')
            ->orderBy('match_number')
            ->get();

        $this->info("\nUpdated Matches:");
        foreach ($matches as $match) {
            $this->line("Match ID: {$match->id}");
            $this->line("Round: {$match->round}");
            $this->line("Bracket: {$match->bracket}");
            $this->line("Match Number: {$match->match_number}");
            $this->line("Team 1: " . ($match->team1 ? $match->team1->team_name : 'TBD'));
            $this->line("Team 2: " . ($match->team2 ? $match->team2->team_name : 'TBD'));
            $this->line("Winner: " . ($match->winner ? $match->winner->team_name : 'TBD'));
            $this->line("-------------------");
        }
    }
}
