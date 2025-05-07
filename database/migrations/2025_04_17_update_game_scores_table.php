<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('db_game_score', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['name', 'best_of', 'max_round', 'winner', 'condition']);

            // Add new columns
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->json('player_scores')->nullable();
        });
    }

    public function down()
    {
        Schema::table('db_game_score', function (Blueprint $table) {
            // Remove new columns
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id', 'player_scores']);

            // Restore old columns
            $table->string('name');
            $table->integer('best_of');
            $table->integer('max_round');
            $table->integer('winner');
            $table->string('condition');
        });
    }
};