<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseGame extends Model
{
    protected $fillable = [
        'name',
        'parameters',
        'is_created',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_created' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!preg_match('/^db_game_[a-z0-9_]+$/', $model->name)) {
                throw new \Exception('Database name must start with db_game_ and contain only lowercase letters, numbers, and underscores');
            }
        });
    }
}
