<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competition extends Model
{
    protected $fillable = [
        'title',
        'description',
        'picture',
        'registration_start',
        'registration_end',
        'tournament_start',
        'tournament_end',
        'team_count',
        'status',
        'tournament_type'
    ];

    protected $casts = [
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'tournament_start' => 'datetime',
        'tournament_end' => 'datetime',
    ];

    public function teamCompetitions(): HasMany
    {
        return $this->hasMany(TeamCompetition::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_competitions')
            ->withPivot('status', 'rejection_reason')
            ->withTimestamps();
    }

    public function approvedTeams()
    {
        return $this->teams()->wherePivot('status', 'approved');
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class)->orderBy('match_number');
    }

    public function getRemainingSlots()
    {
        $approvedTeams = $this->teams()
            ->wherePivot('status', 'approved')
            ->count();

        return max(0, $this->team_count - $approvedTeams);
    }

    public function getStatus()
    {
        $now = Carbon::now();

        if ($now < $this->registration_start) {
            return 'upcoming';
        } elseif ($now >= $this->registration_start && $now <= $this->registration_end) {
            return 'registration_open';
        } elseif ($now > $this->registration_end && $now < $this->tournament_start) {
            return 'registration_closed';
        } elseif ($now >= $this->tournament_start && $now <= $this->tournament_end) {
            return 'ongoing';
        } else {
            return 'completed';
        }
    }

    public function show(Competition $competition)
    {
        $competition->load(['matches.team1', 'matches.team2', 'teams']);
        return view('competition.show', compact('competition'));
    }
}