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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('game_id')->unique();
            $table->string('name');
            $table->string('picture')->nullable();
            $table->string('database_name')->unique();
            $table->string('credential_database_name')->unique();
            $table->json('parameters')->nullable();
            $table->json('credential_parameters')->nullable();
            $table->boolean('is_created')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
