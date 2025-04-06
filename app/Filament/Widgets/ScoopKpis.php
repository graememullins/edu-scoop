<?php

namespace App\Filament\Widgets;

use App\Models\TeachingJob;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ScoopKpis extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $region = $this->filters['region'] ?? null;
        $professionId = $this->filters['profession_id'] ?? null;
        $professionGroupId = $this->filters['profession_group_id'] ?? null;

        $baseQuery = TeachingJob::query()
            ->when($region, fn ($query) => $query->where('region', $region))
            ->when($professionId, fn ($query) =>
                $query->whereHas('keyword', fn ($q) =>
                    $q->where('profession_id', $professionId)
                )
            )
            ->when($professionGroupId, fn ($query) =>
                $query->whereHas('keyword.profession', fn ($q) =>
                    $q->where('profession_group_id', $professionGroupId)
                )
            );

        // Top professions
        $topProfessions = (clone $baseQuery)
            ->select('professions.name', DB::raw('COUNT(*) as total'))
            ->join('keywords', 'teaching_jobs.keyword_id', '=', 'keywords.id')
            ->join('professions', 'keywords.profession_id', '=', 'professions.id')
            ->groupBy('professions.name')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('professions.name')
            ->toArray();

        // Top profession groups
        $topGroups = (clone $baseQuery)
            ->join('keywords', 'teaching_jobs.keyword_id', '=', 'keywords.id')
            ->join('professions', 'keywords.profession_id', '=', 'professions.id')
            ->join('profession_groups', 'professions.profession_group_id', '=', 'profession_groups.id')
            ->select('profession_groups.name', DB::raw('COUNT(*) as total'))
            ->groupBy('profession_groups.name')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('profession_groups.name')
            ->toArray();

        // Most active poster
        $topPoster = (clone $baseQuery)
            ->select('posted_by', DB::raw('COUNT(*) as total'))
            ->whereNotNull('posted_by')
            ->groupBy('posted_by')
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

            Stat::make('Top Groups', implode(', ', $topGroups))
                ->description('Most in-demand profession groups'),

            Stat::make('Top Posters', $topPoster?->posted_by ?? 'N/A')
                ->description($topPoster ? $topPoster->total . ' jobs' : ''),

            Stat::make('Top Roles', implode(', ', $topTitles))
                ->description('Most in-demand job titles'),
        ];
    }
}