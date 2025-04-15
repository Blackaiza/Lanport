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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('team_picture')->nullable();
            $table->enum('game', ['Valorant', 'Mobile Legends']);
            $table->enum('status', ['pending', 'active'])->default('pending');
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


        // Schema::table('teams', function (Blueprint $table) {
        //     // $table->string('team_picture')->nullable();
        //     $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
        //     $table->string('game');
        //     $table->enum('status', ['pending', 'active'])->default('pending');
        // });


        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade'); // Member belongs to a team
            $table->enum('role', ['leader', 'member'])->default('member'); // Role in the team
        });

        // Ensure only one leader per team
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['team_id', 'role'], 'unique_team_leader')->where('role', 'leader');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('unique_team_leader');
            $table->dropColumn('team_id');
            $table->dropColumn('role');
        });

        Schema::dropIfExists('teams');
    }
};
