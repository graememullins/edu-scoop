<?php

namespace App\Filament\Widgets;

use App\Models\NhsEnglandJob;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class HealthScoopKpis extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $region = $this->filters['region'] ?? null;
        $professionId = $this->filters['profession_id'] ?? null;

        $baseQuery = NhsEnglandJob::query()
            ->when($region, fn ($query) => $query->where('region', $region))
            ->when($professionId, fn ($query) =>
                $query->whereHas('keyword', fn ($q) =>
                    $q->where('profession_id', $professionId)
                )
            );

        // Top professions
        $topProfessions = (clone $baseQuery)
            ->select('professions.name', DB::raw('COUNT(*) as total'))
            ->join('keywords', 'nhs_england_jobs.keyword_id', '=', 'keywords.id')
            ->join('professions', 'keywords.profession_id', '=', 'professions.id')
            ->groupBy('professions.name')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('professions.name')
            ->toArray();

        // Weekly change
        $thisWeek = (clone $baseQuery)
            ->whereBetween('created_at', [now()->startOfWeek(), now()])
            ->count();

        $lastWeek = (clone $baseQuery)
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        $change = $lastWeek > 0
            ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1)
            : 0;

        // Most active trust
        $topTrust = (clone $baseQuery)
            ->select('trust', DB::raw('COUNT(*) as total'))
            ->whereNotNull('trust')
            ->groupBy('trust')
            ->orderByDesc('total')
            ->first();

        // Top job titles
        $topTitles = (clone $baseQuery)
            ->select('job_title', DB::raw('COUNT(*) as total'))
            ->whereNotNull('job_title')
            ->groupBy('job_title')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('job_title')
            ->toArray();

        return [
            Stat::make('Top Professions', implode(', ', $topProfessions))
            ->description('Most in-demand professions'),

            Stat::make('Weekly Change', ($change >= 0 ? '+' : '') . $change . '%')
                ->description('vs last week')
                ->color($change >= 0 ? 'success' : 'danger'),

            Stat::make('Top Posters', $topTrust?->trust ?? 'N/A')
                ->description($topTrust ? $topTrust->total . ' jobs' : ''),

            Stat::make('Top Roles', implode(', ', $topTitles))
                ->description('Most in-demand job titles'),
        ];
    }
}