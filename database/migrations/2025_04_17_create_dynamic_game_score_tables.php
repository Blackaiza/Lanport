<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Game;

return new class extends Migration
{
    public function up(): void
    {
        // Get all games
        $games = Game::all();

        foreach ($games as $game) {
            $tableName = 'db_game_' . strtolower($game->game_id);

            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('game_id');
                $table->foreignId('competition_id')->constrained()->onDelete('cascade');
                $table->foreignId('team_id')->constrained()->onDelete('cascade');
                $table->json('player_scores');
                $table->integer('score')->default(0);
                $table->timestamps();

                $table->foreign('game_id')
                    ->references('game_id')
                    ->on('games')
                    ->onDelete('cascade');
            });
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