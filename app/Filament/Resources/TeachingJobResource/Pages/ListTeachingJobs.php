<?php

namespace App\Filament\Resources\TeachingJobResource\Pages;

use App\Filament\Resources\TeachingJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Filament\Resources\TeachingJobResource\Widgets\JobsPostedWidget;

class ListTeachingJobs extends ListRecords
{
    protected static string $resource = TeachingJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function getTabs(): array
    {
        $tabs = [
            'last_24_hours' => Tab::make()
                ->label('Last 24 Hours')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('posted_date', '>=', Carbon::now()->subHours(24))
                ),
    
            'last_48_hours' => Tab::make()
                ->label('Last 48 Hours')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('posted_date', '>=', Carbon::now()->subHours(48))
                ),
    
            'last_7_days' => Tab::make()
                ->label('Last 7 Days')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('posted_date', '>=', Carbon::now()->subDays(7)->toDateString())
                ),
    
            'last_30_days' => Tab::make()
                ->label('Last 30 Days')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('posted_date', '>=', Carbon::now()->subDays(30)->toDateString())
                ),
        ];
    
        if (!auth()->user()?->hasRole('trial')) {
            $tabs['last_60_days'] = Tab::make()
                ->label('Last 60 Days')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('posted_date', '>=', Carbon::now()->subDays(60)->toDateString())
                );
        }
    
        return $tabs;
    }

    public function getHeaderWidgets(): array
    {
        return [
            JobsPostedWidget::class,
        ];
    }
    
    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
