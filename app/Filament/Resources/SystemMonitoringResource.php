<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemMonitoringResource\Pages;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\URL;

class SystemMonitoringResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'System Monitoring';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 100;

    public static function getPages(): array
    {
        return [
            'index' => Pages\SystemMonitoring::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return 'Live';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}