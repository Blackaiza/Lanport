<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    protected $table = 'db_game_score';

    protected $fillable = [
        'game_id',
        'competition_id',
        'name',
        'best_of',
        'max_round',
        'winner',
        'condition',
        'score',
    ];

    protected $casts = [
        'best_of' => 'integer',
        'max_round' => 'integer',
        'winner' => 'integer',
        'score' => 'integer',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure game_id is lowercase and contains only valid characters
            if (!empty($model->game_id)) {
                $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));
            }

            // Calculate max_round based on best_of
            $model->max_round = $model->best_of;

            // Calculate winner (half of best_of + 1)
            $model->winner = floor($model->best_of / 2) + 1;
        });

        static::updating(function ($model) {
            if ($model->isDirty('game_id')) {
                // Ensure game_id is lowercase and contains only valid characters
                $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));
            }

            if ($model->isDirty('best_of')) {
                // Recalculate max_round and winner when best_of changes
                $model->max_round = $model->best_of;
                $model->winner = floor($model->best_of / 2) + 1;
            }
        });
    }
}
