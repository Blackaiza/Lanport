<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Competition;
use App\Models\TeamCompetition;
use App\Models\Team;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $competitions = Competition::with(['teams.leader', 'teamCompetitions'])->get();
        return view('competition.index', compact('competitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('competition.create',compact('users'));
    }

    // public function edit()
    // {
    //     $competition = Competition::find($id);
    //     return view('competition.edit',compact('competition'));
    // }

    public function show(Competition $competition)
    {
        $competition->load(['matches.team1', 'matches.team2', 'teams']);
        return view('competition.show', compact('competition'));
    }

    public function join(Request $request, Competition $competition)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);

        // Check if user is team leader
        $team = Team::findOrFail($request->team_id);
        if ($team->leader_id !== auth()->id()) {
            return back()->with('error', 'You must be the team leader to join a competition.');
        }

        // Check if team is already registered
        $existingRegistration = TeamCompetition::where('team_id', $request->team_id)
            ->where('competition_id', $competition->id)
            ->exists();

        if ($existingRegistration) {
            return back()->with('error', 'This team is already registered for this competition.');
        }

        // Check if competition is full
        if ($competition->getRemainingSlots() <= 0) {
            return back()->with('error', 'This competition is already full.');
        }

        // Create new team competition entry
        try {
            TeamCompetition::create([
                'team_id' => $team->id,
                'competition_id' => $competition->id,
                'team_name' => $team->name,
                'status' => 'pending'
            ]);

            return back()->with('success', 'Successfully registered for the competition! Waiting for approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while registering for the competition.');
        }
    }
}
