<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserStats;
use App\Filament\Widgets\SystemStats;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-squares-2x2';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?string $slug = 'dashboard';

    protected function getWidgets(): array
    {
        return [
            UserStats::class,
            SystemStats::class,
        ];
    }

    protected static string $view = 'filament.pages.dashboard';
}
