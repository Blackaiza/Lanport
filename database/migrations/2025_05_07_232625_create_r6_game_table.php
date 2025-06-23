<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateR6GameTable extends Migration
{
    public function up(): void
    {
        Schema::create('db_game_r6', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
$table->integer('kills');
$table->integer('deaths');
$table->integer('assists');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_game_r6');
    }
}