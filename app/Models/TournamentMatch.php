<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    protected $fillable = [
        'competition_id',
        'round',
        'match_number',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'winner_id',
        'scheduled_at',
        'bracket_position',
        'tournament_type'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function team1()
    {
        return $this->belongsTo(TeamCompetition::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(TeamCompetition::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(TeamCompetition::class, 'winner_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'next_match_id');
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_competitions')
            ->withPivot('status', 'team_name')
            ->withTimestamps();
    }
}
