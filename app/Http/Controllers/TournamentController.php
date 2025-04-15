<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Match;
use App\Models\Matchs;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::all();
        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Tournament::create(['name' => $request->name]);

        return redirect()->route('tournaments.index');
    }

    public function generateBracket($id)
    {
        $tournament = Tournament::findOrFail($id);
        $teams = $tournament->teams()->get()->shuffle();

        $totalTeams = count($teams);
        $bracketSize = pow(2, ceil(log($totalTeams, 2)));

        for ($i = 0; $i < $bracketSize; $i += 2) {
            if (isset($teams[$i], $teams[$i + 1])) {
                Matchs::create([
                    'tournament_id' => $tournament->id,
                    'round' => 1,
                    'team1_id' => $teams[$i]->id,
                    'team2_id' => $teams[$i + 1]->id,
                ]);
            }
        }

        return redirect()->route('tournaments.show', $id);
    }

    public function show($id)
    {
        $tournament = Tournament::with('matches')->findOrFail($id);
        return view('tournaments.show', compact('tournament'));
    }

    public function updateMatchWinner(Request $request, $id)
    {
        $match = Matchs::findOrFail($id);
        $match->update(['winner_id' => $request->winner_id]);

        $tournament = Tournament::findOrFail($match->tournament_id);
        $nextRound = $match->round + 1;

        if ($match->bracket === 'winners') {
            // Winner moves forward in Winners' Bracket
            $existingMatch = Matchs::where('tournament_id', $tournament->id)
                                  ->where('round', $nextRound)
                                  ->where('bracket', 'winners')
                                  ->where(function ($query) use ($match) {
                                      $query->whereNull('team1_id')->orWhereNull('team2_id');
                                  })->first();

            if ($existingMatch) {
                if (!$existingMatch->team1_id) {
                    $existingMatch->update(['team1_id' => $match->winner_id]);
                } else {
                    $existingMatch->update(['team2_id' => $match->winner_id]);
                }
            } else {
                Matchs::create([
                    'tournament_id' => $tournament->id,
                    'round' => $nextRound,
                    'team1_id' => $match->winner_id,
                    'team2_id' => null,
                    'bracket' => 'winners'
                ]);
            }

            // Loser moves to Losers' Bracket
            Matchs::create([
                'tournament_id' => $tournament->id,
                'round' => $match->round, // Losers play in the same round first
                'team1_id' => $match->team1_id == $request->winner_id ? $match->team2_id : $match->team1_id,
                'bracket' => 'losers'
            ]);
        } else {
            // Eliminate the loser from Losers' Bracket
            $loserId = $match->team1_id == $request->winner_id ? $match->team2_id : $match->team1_id;
            $match->update(['loser_id' => $loserId]);

            // Winner of Losers' Bracket moves forward
            $nextLosersMatch = Matchs::where('tournament_id', $tournament->id)
                                    ->where('round', $nextRound)
                                    ->where('bracket', 'losers')
                                    ->where(function ($query) use ($match) {
                                        $query->whereNull('team1_id')->orWhereNull('team2_id');
                                    })->first();

            if ($nextLosersMatch) {
                if (!$nextLosersMatch->team1_id) {
                    $nextLosersMatch->update(['team1_id' => $match->winner_id]);
                } else {
                    $nextLosersMatch->update(['team2_id' => $match->winner_id]);
                }
            } else {
                Matchs::create([
                    'tournament_id' => $tournament->id,
                    'round' => $nextRound,
                    'team1_id' => $match->winner_id,
                    'team2_id' => null,
                    'bracket' => 'losers'
                ]);
            }
        }

        return redirect()->back();
    }


}

