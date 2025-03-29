<?php

namespace App\Filament\Widgets;

use App\Models\NhsEnglandJob;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class JobsPostedWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;

    protected function getCards(): array
    {
        $region = $this->filters['region'] ?? null;
        $professionId = $this->filters['profession_id'] ?? null;
    
        $baseQuery = NhsEnglandJob::query()
            ->when($region, fn($query) => $query->where('region', $region))
            ->when($professionId, function ($query, $professionId) {
                $query->whereHas('keyword', fn ($q) =>
                    $q->where('profession_id', $professionId)
                );
            });
    
        $count_7 = (clone $baseQuery)->whereDate('posted_date', '>=', now()->subDays(7))->count();
        $count_30 = (clone $baseQuery)->whereDate('posted_date', '>=', now()->subDays(30))->count();
        $count_60 = (clone $baseQuery)->whereDate('posted_date', '>=', now()->subDays(60))->count();
    
        $chart_7 = collect(range(6, 0))
            ->map(fn ($daysAgo) => (clone $baseQuery)
                ->whereDate('posted_date', now()->subDays($daysAgo))
                ->count()
            )
            ->toArray();
    
        $chart_30 = collect(range(29, 0, 5))
            ->map(fn ($daysAgo) => (clone $baseQuery)
                ->whereBetween('posted_date', [
                    now()->subDays($daysAgo + 5),
                    now()->subDays($daysAgo),
                ])
                ->count()
            )
            ->toArray();
    
        $chart_60 = collect(range(55, 0, 10))
            ->map(fn ($daysAgo) => (clone $baseQuery)
                ->whereBetween('posted_date', [
                    now()->subDays($daysAgo + 10),
                    now()->subDays($daysAgo),
                ])
                ->count()
            )
            ->toArray();
    
        return [
            Card::make('Jobs Posted (Last 7 Days)', number_format($count_7))
                ->description('Since ' . now()->subDays(7)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_7),
    
            Card::make('Jobs Posted (Last 30 Days)', number_format($count_30))
                ->description('Since ' . now()->subDays(30)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_30),
    
            Card::make('Jobs Posted (Last 60 Days)', number_format($count_60))
                ->description('Since ' . now()->subDays(60)->toFormattedDateString())
                ->color('success')
                ->icon('heroicon-m-briefcase')
                ->chart($chart_60),
        ];
    }    

    public static function canView(): bool
    {
        return true;
    }
}
