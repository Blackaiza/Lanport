<?php

namespace App\Filament\Resources\TournamentResource;

use App\Models\Competition;
use App\Models\TournamentMatch;
use App\Models\TeamCompetition;
use Filament\Notifications\Notification;

class SingleEliminationFormat
{
    public static function generate(Competition $competition, $teams)
    {
        $teams = $teams->shuffle();
        $numTeams = $teams->count();
        $totalRounds = ceil(log($numTeams, 2)); // Calculate total number of rounds

        // Define round names based on the number of teams
        $roundNames = [
            2 => ['finals'],
            4 => ['semi_finals', 'finals'],
            8 => ['quarter_finals', 'semi_finals', 'finals'],
            16 => ['round_of_16', 'quarter_finals', 'semi_finals', 'finals'],
            32 => ['round_of_32', 'round_of_16', 'quarter_finals', 'semi_finals', 'finals'],
            64 => ['round_of_64', 'round_of_32', 'round_of_16', 'quarter_finals', 'semi_finals', 'finals'],
        ];

        $matchNumber = 1;
        $matchDate = $competition->tournament_start;

        // Generate first round matches
        $firstRoundName = $roundNames[$numTeams][0] ?? "round_1";
        for ($i = 0; $i < $numTeams; $i += 2) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => $firstRoundName,
                'match_number' => $matchNumber,
                'team1_id' => $teams[$i]->id,
                'team2_id' => isset($teams[$i + 1]) ? $teams[$i + 1]->id : null,
                'scheduled_at' => $matchDate,
                'bracket_position' => $matchNumber,
                'tournament_type' => 'single_elimination',
            ]);
            $matchNumber++;
            $matchDate = $matchDate->addHours(2);
        }

        // Generate subsequent rounds
        $remainingTeams = ceil($numTeams / 2);
        $currentRound = 2;

        while ($remainingTeams > 1) {
            $roundName = $roundNames[$numTeams][$currentRound - 1] ?? "round_{$currentRound}";
            $matchesInRound = ceil($remainingTeams / 2);

            for ($match = 1; $match <= $matchesInRound; $match++) {
                TournamentMatch::create([
                    'competition_id' => $competition->id,
                    'round' => $roundName,
                    'match_number' => $match,
                    'scheduled_at' => null,
                    'bracket_position' => $match,
                    'tournament_type' => 'single_elimination',
                ]);
            }

            $remainingTeams = ceil($remainingTeams / 2);
            $currentRound++;
        }
    }

    public static function completeRound(Competition $competition, string $round, ?int $numTeams = null)
    {
        $matches = TournamentMatch::where('competition_id', $competition->id)
            ->where('round', $round)
            ->orderBy('match_number')
            ->get();

        // If numTeams is not provided, calculate it from the competition
        if ($numTeams === null) {
            $numTeams = TeamCompetition::where('competition_id', $competition->id)
                ->where('status', 'approved')
                ->count();
        }

        foreach ($matches as $match) {
            // Validate scores
            if (!isset($match->team1_score) || !isset($match->team2_score)) {
                Notification::make()
                    ->title('Missing Scores')
                    ->body("Please enter scores for Match {$match->match_number}")
                    ->warning()
                    ->send();
                return;
            }

            // Check for ties
            if ($match->team1_score == $match->team2_score) {
                Notification::make()
                    ->title('Tied Match')
                    ->body("Match {$match->match_number} is tied. There must be a winner.")
                    ->warning()
                    ->send();
                return;
            }

            // Determine winner
            $winner_id = $match->team1_score > $match->team2_score ? $match->team1_id : $match->team2_id;

            // Update current match with winner
            $match->update(['winner_id' => $winner_id]);

            // If this is not the finals, advance the winner to the next round
            if ($round !== 'finals') {
                // Calculate next match number (half of current match number, rounded up)
                $nextMatchNumber = ceil($match->match_number / 2);

                // Determine next round name based on current round
                $roundNames = [
                    'round_1' => 'round_2',
                    'round_2' => 'round_of_16',
                    'round_of_16' => 'quarter_finals',
                    'quarter_finals' => 'semi_finals',
                    'semi_finals' => 'finals',
                ];

                $nextRound = $roundNames[$round] ?? null;

                if ($nextRound) {
                    // Find the next match
                    $nextMatch = TournamentMatch::where('competition_id', $competition->id)
                        ->where('round', $nextRound)
                        ->where('match_number', $nextMatchNumber)
                        ->first();

                    if ($nextMatch) {
                        // If the current match number is odd, set team1, otherwise set team2
                        if ($match->match_number % 2 == 1) {
                            $nextMatch->update(['team1_id' => $winner_id]);
                        } else {
                            $nextMatch->update(['team2_id' => $winner_id]);
                        }

                        // Notify about the advancement
                        $winningTeam = TeamCompetition::with('team')->find($winner_id);
                        Notification::make()
                            ->title('Winner Advanced')
                            ->body("{$winningTeam->team->name} advances to the next round!")
                            ->success()
                            ->send();
                    }
                }
            } else {
                // If this is the finals, mark the winner
                $match->update(['is_final_winner' => true]);

                // Update competition status to completed
                $competition->update(['status' => 'completed']);

                // Notify about the champion
                $champion = TeamCompetition::with('team')->find($winner_id);
                Notification::make()
                    ->title('🏆 Champion Crowned 🏆')
                    ->body("{$champion->team->name} is the tournament champion!")
                    ->success()
                    ->send();
            }
        }
    }
}
