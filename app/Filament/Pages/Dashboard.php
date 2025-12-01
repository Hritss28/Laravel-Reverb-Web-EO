<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;

class Dashboard extends BaseDashboard
{
    public function getHeaderActions(): array
    {
        return [
            Action::make('realtime_dashboard')
                ->label('Real-time Dashboard')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->url(route('realtime.dashboard'))
                ->openUrlInNewTab(),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\LatestPeminjamans::class,
        ];
    }
}