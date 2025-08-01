<?php

namespace App\Filament\Widgets;

use App\Models\File;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Files', File::count())
                ->description('All uploaded files')
                ->descriptionIcon('heroicon-m-document')
                ->color('primary'),

            Stat::make('Active Files', File::where('expires_at', '>', now())->count())
                ->description('Files not expired')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make('Premium Files', File::where('is_premium', true)->count())
                ->description('Paid upgrades')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Revenue', '$' . number_format(Payment::where('status', 'completed')->sum('amount'), 2))
                ->description('From completed payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
