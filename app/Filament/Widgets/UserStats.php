<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UserStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $activeUsers = User::where('updated_at', '>=', now()->subDays(30))->count();
        $userGrowth = $totalUsers > 0 ? round(($newUsersThisMonth / $totalUsers) * 100, 2) : 0;

        return [
            Card::make('Total Users', $totalUsers)
                ->description($userGrowth . '% growth this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Card::make('New Users This Month', $newUsersThisMonth)
                ->description('New registrations')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),
            Card::make('Recently Updated Users (30 days)', $activeUsers)
                ->description('Users with recent activity')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
