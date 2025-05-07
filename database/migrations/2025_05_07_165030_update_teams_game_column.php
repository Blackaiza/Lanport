<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // First drop the enum constraint
            $table->string('game')->change();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Revert back to enum if needed
            $table->enum('game', ['Valorant', 'Mobile Legends'])->change();
        });
    }
};
