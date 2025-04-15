<?php

namespace App\Filament\Widgets;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class SystemStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalTeams = Team::count();
        $totalTournaments = Tournament::count();
        $totalMatches = TournamentMatch::count();
        $databaseSize = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size FROM information_schema.TABLES WHERE table_schema = ?", [DB::getDatabaseName()])[0]->size;

        return [
            Card::make('Total Teams', $totalTeams)
                ->description('Registered teams')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Card::make('Total Tournaments', $totalTournaments)
                ->description('Active and completed tournaments')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
            Card::make('Total Matches', $totalMatches)
                ->description('Matches played')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
            Card::make('Database Size', $databaseSize . ' MB')
                ->description('Total database size')
                ->descriptionIcon('heroicon-m-server')
                ->color('gray'),
        ];
    }
}