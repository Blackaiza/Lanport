<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    protected $table = 'db_game_score';

    protected $fillable = [
        'game_id',
        'competition_id',
        'team_id',
        'score',
        'player_scores',
    ];

    protected $casts = [
        'player_scores' => 'array',
        'score' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (isset($attributes['game_id'])) {
            $this->setTable('db_game_' . strtolower($attributes['game_id']));
        }
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Get game_id from competition if not set
            if (empty($model->game_id) && $model->competition_id) {
                $competition = Competition::find($model->competition_id);
                if ($competition) {
                    $model->game_id = $competition->game_id;
                }
            }

            // Ensure game_id is lowercase and contains only valid characters
            if (!empty($model->game_id)) {
                $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));
                $model->setTable('db_game_' . $model->game_id);
            }

            // Calculate total score from player scores
            if (!empty($model->player_scores)) {
                $totalScore = 0;
                foreach ($model->player_scores as $playerScore) {
                    $totalScore += ($playerScore['kills'] * 2) + $playerScore['assists'] - $playerScore['deaths'];
                }
                $model->score = $totalScore;
            }
        });

        static::updating(function ($model) {
            // Get game_id from competition if not set
            if (empty($model->game_id) && $model->competition_id) {
                $competition = Competition::find($model->competition_id);
                if ($competition) {
                    $model->game_id = $competition->game_id;
                }
            }

            if ($model->isDirty('game_id')) {
                // Ensure game_id is lowercase and contains only valid characters
                $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));
                $model->setTable('db_game_' . $model->game_id);
            }

            // Recalculate total score if player scores changed
            if ($model->isDirty('player_scores')) {
                $totalScore = 0;
                foreach ($model->player_scores as $playerScore) {
                    $totalScore += ($playerScore['kills'] * 2) + $playerScore['assists'] - $playerScore['deaths'];
                }
                $model->score = $totalScore;
            }
        });
    }
}
