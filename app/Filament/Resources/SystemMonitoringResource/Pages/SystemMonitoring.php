<?php

namespace App\Filament\Resources\SystemMonitoringResource\Pages;

use App\Filament\Resources\SystemMonitoringResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\URL;

class SystemMonitoring extends Page
{
    protected static string $resource = SystemMonitoringResource::class;
    protected static string $view = 'filament.resources.system-monitoring-resource.pages.system-monitoring';

    protected function getActions(): array
    {
        return [
            Action::make('open_pulse')
                ->label('Open Laravel Pulse')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(URL::to('/pulse'))
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SystemMonitoringResource\Widgets\SystemStats::class,
        ];
    }
}
