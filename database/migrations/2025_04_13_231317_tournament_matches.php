<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->string('round')->default('round_1');
            $table->integer('match_number')->default(1);
            $table->foreignId('team1_id')->nullable()->constrained('team_competitions');
            $table->foreignId('team2_id')->nullable()->constrained('team_competitions');
            $table->integer('team1_score')->nullable();
            $table->integer('team2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('team_competitions');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('bracket_position')->default(0);
            $table->foreignId('next_match_id')->nullable()->constrained('tournament_matches');
            $table->string('tournament_type')->default('single_elimination');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
