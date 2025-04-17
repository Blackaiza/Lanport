<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'picture',
        'database_name',
        'credential_database_name',
        'parameters',
        'credential_parameters',
        'is_created',
    ];

    protected $casts = [
        'parameters' => 'array',
        'credential_parameters' => 'array',
        'is_created' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Check if game_id is set and not empty
            if (empty($model->game_id)) {
                throw new \Exception('Game ID is required');
            }

            // Ensure game_id is lowercase and contains only valid characters
            $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));

            // Set database names
            $model->database_name = 'db_game_' . $model->game_id;
            $model->credential_database_name = 'game_credential_' . $model->game_id . '_Players';

            // Initialize empty arrays if not set
            if (!isset($model->parameters)) {
                $model->parameters = [];
            }
            if (!isset($model->credential_parameters)) {
                $model->credential_parameters = [];
            }

            // Log the values for debugging
            \Log::info('Creating game with ID: ' . $model->game_id);
            \Log::info('Generated database name: ' . $model->database_name);
            \Log::info('Generated credential database name: ' . $model->credential_database_name);
            \Log::info('Parameters: ' . json_encode($model->parameters));
            \Log::info('Credential Parameters: ' . json_encode($model->credential_parameters));
        });

        static::updating(function ($model) {
            if ($model->isDirty('game_id')) {
                // Ensure game_id is lowercase and contains only valid characters
                $model->game_id = strtolower(preg_replace('/[^a-z0-9_]/', '', $model->game_id));

                // Update database names
                $model->database_name = 'db_game_' . $model->game_id;
                $model->credential_database_name = 'game_credential_' . $model->game_id . '_Players';
            }
        });
    }

    public function validateDatabaseNames()
    {
        if (!$this->database_name || !$this->credential_database_name) {
            throw new \Exception('Database names are not properly set');
        }

        // Debug the database name
        \Log::info('Validating database name: ' . $this->database_name);
        \Log::info('Game ID: ' . $this->game_id);

        // Clean the game_id to ensure it only contains valid characters
        $cleanGameId = strtolower(preg_replace('/[^a-z0-9_]/', '', $this->game_id));
        $expectedDatabaseName = 'db_game_' . $cleanGameId;

        if ($this->database_name !== $expectedDatabaseName) {
            \Log::error('Database name mismatch. Expected: ' . $expectedDatabaseName . ', Got: ' . $this->database_name);
            throw new \Exception('Database name does not match expected format');
        }

        if (!preg_match('/^db_game_[a-z0-9_]+$/', $this->database_name)) {
            \Log::error('Invalid database name format: ' . $this->database_name);
            throw new \Exception('Invalid main database name format. Database name must start with "db_game_" and contain only lowercase letters, numbers, and underscores.');
        }

        if (!preg_match('/^game_credential_[a-z0-9_]+_Players$/', $this->credential_database_name)) {
            throw new \Exception('Invalid credential database name format');
        }

        return true;
    }

    // Add this method to support the relationship in the Select component
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }
}
