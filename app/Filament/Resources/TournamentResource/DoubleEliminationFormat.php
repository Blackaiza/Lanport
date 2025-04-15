<?php

namespace App\Filament\Resources\TournamentResource;

use App\Models\Competition;
use App\Models\TournamentMatch;
use App\Models\TeamCompetition;
use Filament\Notifications\Notification;

class DoubleEliminationFormat
{
    public static function generate(Competition $competition, $teams)
    {
        $teams = $teams->shuffle();
        $numTeams = $teams->count();
        $numRounds = ceil(log($numTeams, 2));

        $matchNumber = 1;
        $matchDate = $competition->tournament_start;

        // Store all first round matchups for notification
        $firstRoundMatchups = [];

        // Generate Upper Bracket Round 1 matches with initial team assignments
        $upperRound1Matches = [];
        for ($i = 0; $i < $numTeams; $i += 2) {
            $team1 = $teams[$i];
            $team2 = isset($teams[$i + 1]) ? $teams[$i + 1] : null;

            $match = TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'upper_round_1',
                'match_number' => $matchNumber,
                'team1_id' => $team1->id,
                'team2_id' => $team2?->id,
                'scheduled_at' => $matchDate,
                'bracket_position' => $matchNumber,
                'tournament_type' => 'double_elimination',
                'bracket' => 'upper'
            ]);

            $upperRound1Matches[] = $match;
            $firstRoundMatchups[] = [
                'match_number' => $matchNumber,
                'team1' => $team1->team->name,
                'team2' => $team2?->team->name ?? 'BYE'
            ];

            $matchNumber++;
            $matchDate = $matchDate->addHours(2);
        }

        // Send notifications for all first round matchups
        $matchupMessage = "Upper Bracket - Round 1 Matchups:\n";
        foreach ($firstRoundMatchups as $matchup) {
            $matchupMessage .= "Match {$matchup['match_number']}: {$matchup['team1']} vs {$matchup['team2']}\n";
        }

        Notification::make()
            ->title('Tournament Matchups Set')
            ->body($matchupMessage)
            ->success()
            ->send();

        // Calculate number of matches for each round
        $numUpperRound1Matches = count($upperRound1Matches);
        $numUpperRound2Matches = ceil($numUpperRound1Matches / 2);
        $numUpperQuarterFinals = ceil($numUpperRound2Matches / 2);
        $numSemiFinals = ceil($numUpperQuarterFinals / 2);

        // Generate Lower Round 1 matches (half of Upper Round 1 matches)
        $numLowerRound1Matches = ceil($numUpperRound1Matches / 2);
        for ($match = 1; $match <= $numLowerRound1Matches; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'lower_round_1',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'lower'
            ]);
        }

        // Generate Upper Round 2 matches
        for ($match = 1; $match <= $numUpperRound2Matches; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'upper_round_2',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'upper'
            ]);
        }

        // Generate Lower Round 2 matches (same as Lower Round 1 matches)
        for ($match = 1; $match <= $numLowerRound1Matches; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'lower_round_2',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'lower'
            ]);
        }

        // Generate Upper Quarter Finals matches
        for ($match = 1; $match <= $numUpperQuarterFinals; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'upper_quarter_finals',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'upper'
            ]);
        }

        // Generate Lower Quarter Finals matches
        $numLowerQuarterFinals = ceil($numLowerRound1Matches / 2);
        for ($match = 1; $match <= $numLowerQuarterFinals; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'lower_quarter_finals',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'lower'
            ]);
        }

        // Generate Semi Finals matches
        for ($match = 1; $match <= $numSemiFinals; $match++) {
            TournamentMatch::create([
                'competition_id' => $competition->id,
                'round' => 'semi_finals',
                'match_number' => $match,
                'scheduled_at' => null,
                'bracket_position' => $match,
                'tournament_type' => 'double_elimination',
                'bracket' => 'semi'
            ]);
        }

        // Generate Finals match
        TournamentMatch::create([
            'competition_id' => $competition->id,
            'round' => 'finals',
            'match_number' => 1,
            'scheduled_at' => null,
            'bracket_position' => 1,
            'tournament_type' => 'double_elimination',
            'bracket' => 'finals'
        ]);

        // Generate Grand Finals match
        TournamentMatch::create([
            'competition_id' => $competition->id,
            'round' => 'grand_finals',
            'match_number' => 1,
            'scheduled_at' => null,
            'bracket_position' => 1,
            'tournament_type' => 'double_elimination',
            'bracket' => 'grand_finals'
        ]);
    }

    public static function completeRound(Competition $competition, string $round, int $numTeams = null)
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

            // Determine winner and loser
            $winner_id = $match->team1_score > $match->team2_score ? $match->team1_id : $match->team2_id;
            $loser_id = $match->team1_score > $match->team2_score ? $match->team2_id : $match->team1_id;

            // Update current match with winner
            $match->update(['winner_id' => $winner_id]);

            // Handle advancement based on round type
            if (str_starts_with($round, 'upper_')) {
                // Upper bracket matches
                $nextUpperRound = static::getNextUpperRound($round);
                $nextLowerRound = static::getLowerRoundForUpperLoser($round);

                // Advance winner to next upper round
                if ($nextUpperRound) {
                    if ($round === 'upper_round_1') {
                        // Calculate which Upper Round 2 match this winner should go to
                        $upperRound2MatchNumber = ceil($match->match_number / 2);

                        // Find the Upper Round 2 match
                        $upperRound2Match = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'upper_round_2')
                            ->where('match_number', $upperRound2MatchNumber)
                            ->first();

                        if ($upperRound2Match) {
                            // Determine which slot to put the winner in
                            if ($match->match_number % 2 == 1) {
                                // First match of the pair goes to team1
                                $upperRound2Match->update([
                                    'team1_id' => $winner_id,
                                    'team1_score' => null
                                ]);
                            } else {
                                // Second match of the pair goes to team2
                                $upperRound2Match->update([
                                    'team2_id' => $winner_id,
                                    'team2_score' => null
                                ]);
                            }

                            // Schedule the match if both teams are set
                            if ($upperRound2Match->team1_id && $upperRound2Match->team2_id) {
                                $upperRound2Match->update(['scheduled_at' => now()->addHours(2)]);
                            }

                            // Send notification about the advancement
                            $winningTeam = TeamCompetition::with('team')->find($winner_id);
                            Notification::make()
                                ->title('Team Advances to Upper Round 2')
                                ->body("{$winningTeam->team->name} advances to Upper Round 2")
                                ->success()
                                ->send();
                        }
                    } else if ($round === 'upper_round_2') {
                        // Calculate which Upper Quarter Finals match this winner should go to
                        $upperQuarterFinalsMatchNumber = ceil($match->match_number / 2);

                        // Find the Upper Quarter Finals match
                        $upperQuarterFinalsMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'upper_quarter_finals')
                            ->where('match_number', $upperQuarterFinalsMatchNumber)
                            ->first();

                        if ($upperQuarterFinalsMatch) {
                            // Determine which slot to put the winner in
                            if ($match->match_number % 2 == 1) {
                                // First match of the pair goes to team1
                                $upperQuarterFinalsMatch->update([
                                    'team1_id' => $winner_id,
                                    'team1_score' => null
                                ]);
                            } else {
                                // Second match of the pair goes to team2
                                $upperQuarterFinalsMatch->update([
                                    'team2_id' => $winner_id,
                                    'team2_score' => null
                                ]);
                            }

                            // Schedule the match if both teams are set
                            if ($upperQuarterFinalsMatch->team1_id && $upperQuarterFinalsMatch->team2_id) {
                                $upperQuarterFinalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                            }

                            // Send notification about the advancement
                            $winningTeam = TeamCompetition::with('team')->find($winner_id);
                            Notification::make()
                                ->title('Team Advances to Upper Quarter Finals')
                                ->body("{$winningTeam->team->name} advances to Upper Quarter Finals")
                                ->success()
                                ->send();
                        }

                        // Losers go to Lower Round 2
                        $lowerRound2MatchNumber = $match->match_number;
                        $lowerRound2Match = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'lower_round_2')
                            ->where('match_number', $lowerRound2MatchNumber)
                            ->first();

                        if ($lowerRound2Match) {
                            $lowerRound2Match->update([
                                'team1_id' => $loser_id,
                                'team1_score' => null
                            ]);

                            // Schedule the match if both teams are set
                            if ($lowerRound2Match->team1_id && $lowerRound2Match->team2_id) {
                                $lowerRound2Match->update(['scheduled_at' => now()->addHours(2)]);
                            }
                        }
                    } else if ($round === 'upper_quarter_finals') {
                        // For Upper Quarter Finals, winners advance to Finals
                        $finalsMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'finals')
                            ->first();

                        if ($finalsMatch) {
                            $finalsMatch->update([
                                'team1_id' => $winner_id,
                                'team1_score' => null
                            ]);

                            // Schedule the match if both teams are set
                            if ($finalsMatch->team1_id && $finalsMatch->team2_id) {
                                $finalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                            }

                            // Send notification about the advancement
                            $winningTeam = TeamCompetition::with('team')->find($winner_id);
                            Notification::make()
                                ->title('Team Advances to Finals')
                                ->body("{$winningTeam->team->name} advances to Finals")
                                ->success()
                                ->send();
                        }

                        // Losers from Upper Quarter Finals go to Semi Finals as team1
                        $semiFinalsMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'semi_finals')
                            ->first();

                        if ($semiFinalsMatch) {
                            $semiFinalsMatch->update([
                                'team1_id' => $loser_id,
                                'team1_score' => null
                            ]);

                            // Schedule the match if both teams are set
                            if ($semiFinalsMatch->team1_id && $semiFinalsMatch->team2_id) {
                                $semiFinalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                            }

                            // Send notification about the advancement
                            $losingTeam = TeamCompetition::with('team')->find($loser_id);
                            Notification::make()
                                ->title('Team Advances to Semi Finals')
                                ->body("{$losingTeam->team->name} advances to Semi Finals")
                                ->success()
                                ->send();
                        }
                    }
                }

                // Send loser to lower bracket
                if ($nextLowerRound) {
                    // For Upper Round 1, we need to handle the pairing of losers differently
                    if ($round === 'upper_round_1') {
                        // Calculate which lower round 1 match this loser should go to
                        $lowerMatchNumber = ceil($match->match_number / 2);
                        $lowerMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'lower_round_1')
                            ->where('match_number', $lowerMatchNumber)
                            ->first();

                        if ($lowerMatch) {
                            // If this is the first match of the pair, put loser in team1
                            // If this is the second match of the pair, put loser in team2
                            if ($match->match_number % 2 == 1) {
                                $lowerMatch->update(['team1_id' => $loser_id, 'team1_score' => null]);
                            } else {
                                $lowerMatch->update(['team2_id' => $loser_id, 'team2_score' => null]);
                            }
                        }
                    } else {
                        // For other upper rounds, use standard approach
                        $lowerRound = static::getLowerRoundForUpperLoser($round);
                        $lowerMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', $lowerRound)
                            ->where('match_number', ceil($match->match_number / 2))
                            ->first();

                        if ($lowerMatch) {
                            if ($match->match_number % 2 == 1) {
                                $lowerMatch->update(['team1_id' => $loser_id, 'team1_score' => null]);
                            } else {
                                $lowerMatch->update(['team2_id' => $loser_id, 'team2_score' => null]);
                            }
                        }
                    }
                }
            } else if (str_starts_with($round, 'lower_')) {
                // Lower bracket matches
                $nextLowerRound = static::getNextLowerRound($round);

                // Advance winner to next lower round
                if ($nextLowerRound) {
                    if ($round === 'lower_round_1') {
                        // For Lower Round 1, winners advance to Lower Round 2
                        $lowerRound2MatchNumber = $match->match_number;
                        $lowerRound2Match = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'lower_round_2')
                            ->where('match_number', $lowerRound2MatchNumber)
                            ->first();

                        if ($lowerRound2Match) {
                            $lowerRound2Match->update([
                                'team2_id' => $winner_id,
                                'team2_score' => null
                            ]);

                            // Schedule the match if both teams are set
                            if ($lowerRound2Match->team1_id && $lowerRound2Match->team2_id) {
                                $lowerRound2Match->update(['scheduled_at' => now()->addHours(2)]);
                            }
                        }
                    } else if ($round === 'lower_round_2') {
                        // For Lower Round 2, winners advance to Lower Quarter Finals
                        $lowerQuarterFinalsMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'lower_quarter_finals')
                            ->where('match_number', ceil($match->match_number / 2))
                            ->first();

                        if ($lowerQuarterFinalsMatch) {
                            if ($match->match_number % 2 == 1) {
                                $lowerQuarterFinalsMatch->update([
                                    'team1_id' => $winner_id,
                                    'team1_score' => null
                                ]);
                            } else {
                                $lowerQuarterFinalsMatch->update([
                                    'team2_id' => $winner_id,
                                    'team2_score' => null
                                ]);
                            }

                            // Schedule the match if both teams are set
                            if ($lowerQuarterFinalsMatch->team1_id && $lowerQuarterFinalsMatch->team2_id) {
                                $lowerQuarterFinalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                            }
                        }
                    } else if ($round === 'lower_quarter_finals') {
                        // For Lower Quarter Finals, winners advance to Semi Finals as team2
                        $semiFinalsMatch = TournamentMatch::where('competition_id', $competition->id)
                            ->where('round', 'semi_finals')
                            ->first();

                        if ($semiFinalsMatch) {
                            $semiFinalsMatch->update([
                                'team2_id' => $winner_id,
                                'team2_score' => null
                            ]);

                            // Schedule the match if both teams are set
                            if ($semiFinalsMatch->team1_id && $semiFinalsMatch->team2_id) {
                                $semiFinalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                            }

                            // Send notification about the advancement
                            $winningTeam = TeamCompetition::with('team')->find($winner_id);
                            Notification::make()
                                ->title('Team Advances to Semi Finals')
                                ->body("{$winningTeam->team->name} advances to Semi Finals")
                                ->success()
                                ->send();
                        }
                    }
                }
            } else if ($round === 'semi_finals') {
                // For Semi Finals, winners advance to Finals
                $finalsMatch = TournamentMatch::where('competition_id', $competition->id)
                    ->where('round', 'finals')
                    ->first();

                if ($finalsMatch) {
                    $finalsMatch->update([
                        'team2_id' => $winner_id,
                        'team2_score' => null
                    ]);

                    // Schedule the match if both teams are set
                    if ($finalsMatch->team1_id && $finalsMatch->team2_id) {
                        $finalsMatch->update(['scheduled_at' => now()->addHours(2)]);
                    }

                    // Send notification about the advancement
                    $winningTeam = TeamCompetition::with('team')->find($winner_id);
                    Notification::make()
                        ->title('Team Advances to Finals')
                        ->body("{$winningTeam->team->name} advances to Finals")
                        ->success()
                        ->send();
                }
            } else if ($round === 'finals') {
                // Handle Finals match
                $upperQuarterFinalsWinner = TournamentMatch::where('competition_id', $competition->id)
                    ->where('round', 'upper_quarter_finals')
                    ->whereNotNull('winner_id')
                    ->first();

                if ($upperQuarterFinalsWinner && $match->winner_id !== $upperQuarterFinalsWinner->winner_id) {
                    // If the Upper Quarter Finals winner loses in Finals, create Grand Finals
                    $grandFinalsMatch = TournamentMatch::create([
                        'competition_id' => $competition->id,
                        'round' => 'grand_finals',
                        'match_number' => 1,
                        'team1_id' => $upperQuarterFinalsWinner->winner_id,
                        'team2_id' => $match->winner_id,
                        'scheduled_at' => now()->addHours(2)
                    ]);
                } else {
                    // If the Upper Quarter Finals winner wins in Finals, they are the champion
                    $competition->update(['status' => 'completed']);
                }
            } else if ($round === 'grand_finals') {
                // Grand Finals winner is the champion
                $competition->update(['status' => 'completed']);
            }

            // Notify about the results
            $winningTeam = TeamCompetition::with('team')->find($winner_id);
            $losingTeam = TeamCompetition::with('team')->find($loser_id);

            if (str_starts_with($round, 'lower_')) {
                Notification::make()
                    ->title('Team Eliminated')
                    ->body("{$losingTeam->team->name} has been eliminated from the tournament")
                    ->warning()
                    ->send();
            }

            Notification::make()
                ->title('Match Complete')
                ->body("{$winningTeam->team->name} advances to the next round")
                ->success()
                ->send();
        }

        // After completing Upper Round 1, unlock Lower Round 1
        if ($round === 'upper_round_1') {
            $lowerRound1Matches = TournamentMatch::where('competition_id', $competition->id)
                ->where('round', 'lower_round_1')
                ->get();

            foreach ($lowerRound1Matches as $lowerMatch) {
                if ($lowerMatch->team1_id && $lowerMatch->team2_id) {
                    $lowerMatch->update(['scheduled_at' => now()->addHours(2)]);
                }
            }

            Notification::make()
                ->title('Lower Bracket Unlocked')
                ->body('Lower Round 1 matches are now available')
                ->success()
                ->send();
        }
    }

    protected static function getNextUpperRound($currentRound)
    {
        return match($currentRound) {
            'upper_round_1' => 'upper_round_2',
            'upper_round_2' => 'upper_quarter_finals',
            'upper_quarter_finals' => 'finals',
            default => null
        };
    }

    protected static function getNextLowerRound($currentRound)
    {
        return match($currentRound) {
            'lower_round_1' => 'lower_round_2',
            'lower_round_2' => 'lower_quarter_finals',
            'lower_quarter_finals' => 'semi_finals',
            default => null
        };
    }

    protected static function getLowerRoundForUpperLoser($upperRound)
    {
        return match($upperRound) {
            'upper_round_1' => 'lower_round_1',
            'upper_round_2' => 'lower_round_2',
            'upper_quarter_finals' => 'semi_finals',
            default => 'lower_round_1'
        };
    }
}
