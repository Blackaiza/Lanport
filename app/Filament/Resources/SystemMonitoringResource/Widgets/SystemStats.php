<?php

namespace App\Filament\Resources\SystemMonitoringResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('PHP Version', phpversion())
                ->description('Current PHP Version')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('success'),

            Stat::make('Laravel Version', app()->version())
                ->description('Framework Version')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Server Status', 'Running')
                ->description('System Status')
                ->descriptionIcon('heroicon-m-server')
                ->color('success'),
        ];
    }
}
