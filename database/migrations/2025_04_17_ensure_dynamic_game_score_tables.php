<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all games
        $games = Game::all();

        foreach ($games as $game) {
            $tableName = 'db_game_' . strtolower($game->game_id);

            // Drop the table if it exists
            Schema::dropIfExists($tableName);

            // Create the table with the correct structure
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('competition_id')->constrained()->onDelete('cascade');
                $table->foreignId('team_id')->constrained()->onDelete('cascade');
                $table->json('player_scores');
                $table->integer('score')->default(0);
                $table->timestamps();
            });

            // Copy data from db_game_score if it exists
            if (Schema::hasTable('db_game_score')) {
                $scores = DB::table('db_game_score')
                    ->where('game_id', $game->game_id)
                    ->get();

                foreach ($scores as $score) {
                    DB::table($tableName)->insert([
                        'competition_id' => $score->competition_id,
                        'team_id' => $score->team_id,
                        'player_scores' => $score->player_scores,
                        'score' => $score->score,
                        'created_at' => $score->created_at,
                        'updated_at' => $score->updated_at,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Get all games
        $games = Game::all();

        foreach ($games as $game) {
            $tableName = 'db_game_' . strtolower($game->game_id);
            Schema::dropIfExists($tableName);
        }
    }
};