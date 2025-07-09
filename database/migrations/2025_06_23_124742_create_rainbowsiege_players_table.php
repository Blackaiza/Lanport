<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRainbowsiegePlayersTable extends Migration
{
    public function up(): void
    {
        Schema::create('game_credential_rainbowsiege_Players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->string('SteamID')->length(255)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_credential_rainbowsiege_Players');
    }
}