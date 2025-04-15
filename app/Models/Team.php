<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'game',
        'status',
        'leader_id',
        'team_picture'
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
                    ->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'team_competitions')
                    ->withPivot('status', 'team_name', 'rejection_reason')
                    ->withTimestamps();
    }
}
