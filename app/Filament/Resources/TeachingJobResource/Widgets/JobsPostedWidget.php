<?php

namespace App\Filament\Resources\TeachingJobResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Carbon;
use App\Models\TeachingJob;

class JobsPostedWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getCards(): array
    {
        $count_7 = TeachingJob::whereDate('posted_date', '>=', Carbon::now()->subDays(7))->count();
        $count_30 = TeachingJob::whereDate('posted_date', '>=', Carbon::now()->subDays(30))->count();
        $count_60 = TeachingJob::whereDate('posted_date', '>=', Carbon::now()->subDays(60))->count();
        $count_90 = TeachingJob::whereDate('posted_date', '>=', Carbon::now()->subDays(90))->count();
    
        $chart_7 = collect(range(6, 0))
            ->map(fn ($daysAgo) => TeachingJob::whereDate('posted_date', Carbon::now()->subDays($daysAgo))->count())
            ->toArray();
    
        $chart_30 = collect(range(29, 0, 5)) // every 5 days
            ->map(fn ($daysAgo) => TeachingJob::whereBetween('posted_date', [
                Carbon::now()->subDays($daysAgo + 5),
                Carbon::now()->subDays($daysAgo)
            ])->count())
            ->toArray();
    
        $chart_60 = collect(range(55, 0, 10)) // every 10 days
            ->map(fn ($daysAgo) => TeachingJob::whereBetween('posted_date', [
                Carbon::now()->subDays($daysAgo + 10),
                Carbon::now()->subDays($daysAgo)
            ])->count())
            ->toArray();
    
        $chart_90 = collect(range(75, 0, 15)) // every 15 days
            ->map(fn ($daysAgo) => TeachingJob::whereBetween('posted_date', [
                Carbon::now()->subDays($daysAgo + 15),
                Carbon::now()->subDays($daysAgo)
            ])->count())
            ->toArray();
    
        return [
            Card::make('Jobs Posted (Last 7 Days)', number_format($count_7))
                ->description('Since ' . Carbon::now()->subDays(7)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_7),
    
            Card::make('Jobs Posted (Last 30 Days)', number_format($count_30))
                ->description('Since ' . Carbon::now()->subDays(30)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_30),
    
            Card::make('Jobs Posted (Last 60 Days)', number_format($count_60))
                ->description('Since ' . Carbon::now()->subDays(60)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_60),
    
            Card::make('Jobs Posted (Last 90 Days)', number_format($count_90))
                ->description('Since ' . Carbon::now()->subDays(90)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_90),
        ];
    }  

    public static function canView(): bool
    {
        return true;
    }
}