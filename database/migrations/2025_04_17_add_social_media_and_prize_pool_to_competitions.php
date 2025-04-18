<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('game_id')->nullable();
            $table->string('whatsapp_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->string('discord_link')->nullable();
            $table->json('prize_pool')->nullable();
            $table->boolean('require_player_id')->default(false);

            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropColumn([
                'game_id',
                'whatsapp_link',
                'telegram_link',
                'discord_link',
                'prize_pool',
                'require_player_id'
            ]);
        });
    }
};