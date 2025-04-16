<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $table = 'team_members';

    protected $fillable = ['team_id', 'user_id', 'role'];

    const ROLE_LEADER = 'leader';
    const ROLE_CO_LEADER = 'co_leader';
    const ROLE_MEMBER = 'member';

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
