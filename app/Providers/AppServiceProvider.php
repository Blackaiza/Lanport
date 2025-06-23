<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log component registration
        Log::info('Registering tournament bracket components');

        try {
            Blade::component('tournament-bracket-single-elimination', \App\View\Components\TournamentBracketSingleElimination::class);
            Blade::component('tournament-bracket-double-elimination', \App\View\Components\TournamentBracketDoubleElimination::class);
            // Blade::component('tournament-bracket-round-robin', \App\View\Components\TournamentBracketRoundRobin::class);
            Log::info('Tournament bracket components registered successfully');
        } catch (\Exception $e) {
            Log::error('Error registering tournament bracket components: ' . $e->getMessage());
        }
    }
}
