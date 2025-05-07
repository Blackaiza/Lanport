<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GameScoreSeeder extends Seeder
{
    public function run(): void
    {
        // Create a user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Player One',
            'email' => 'player1@example.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a team
        $teamId = DB::table('teams')->insertGetId([
            'name' => 'Team Alpha',
            'leader_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a game
        DB::table('games')->insert([
            'game_id' => 'valorant',
            'name' => 'Valorant',
            'database_name' => 'db_game_valorant',
            'credential_database_name' => 'game_credential_valorant_Players',
            'is_created' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a competition
        $competitionId = DB::table('competitions')->insertGetId([
            'title' => 'Valorant Tournament',
            'game_id' => 'valorant',
            'registration_start' => now(),
            'registration_end' => now()->addDays(7),
            'tournament_start' => now()->addDays(8),
            'tournament_end' => now()->addDays(15),
            'team_count' => 8,
            'status' => 'registration',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert game scores
        DB::table('db_game_valorant')->insert([
            [
                'competition_id' => $competitionId,
                'name' => 'Match 1',
                'best_of' => 3,
                'max_round' => 13,
                'winner' => 1,
                'condition' => 'win',
                'score' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competitionId,
                'name' => 'Match 2',
                'best_of' => 3,
                'max_round' => 13,
                'winner' => 1,
                'condition' => 'win',
                'score' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
