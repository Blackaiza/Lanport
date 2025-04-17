<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_game_score', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade');
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->string('name');
            $table->integer('best_of')->default(1); // B01, B02, B05, B07
            $table->integer('max_round')->default(1);
            $table->integer('winner')->default(1);
            $table->enum('condition', ['win', 'draw', 'lose'])->default('win');
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_game_score');
    }
};
