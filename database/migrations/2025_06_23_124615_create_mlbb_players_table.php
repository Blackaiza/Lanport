<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlbbPlayersTable extends Migration
{
    public function up(): void
    {
        Schema::create('game_credential_mlbb_Players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->string('ZoneID')->length(255)->unique();
$table->string('AccID')->length(255)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_credential_mlbb_Players');
    }
}