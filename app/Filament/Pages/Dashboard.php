<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Models\TeachingJob;
use App\Models\Profession;
use App\Models\ProfessionGroup;
use Filament\Forms\Components\DatePicker;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Select::make('region')
                ->label('Filter by Region')
                ->options(
                    TeachingJob::query()
                        ->whereNotNull('region')
                        ->distinct()
                        ->orderBy('region')
                        ->pluck('region', 'region')
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->placeholder('All Regions'),
    
            Select::make('profession_group_id')
                ->label('Filter by Profession Group')
                ->options(
                    ProfessionGroup::where('is_active', 1)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->placeholder('All Profession Groups')
                ->reactive(), // needed to trigger the profession dropdown update
    
            Select::make('profession_id')
                ->label('Filter by Profession')
                ->options(function (callable $get) {
                    $groupId = $get('profession_group_id');
    
                    return $groupId
                        ? Profession::where('profession_group_id', $groupId)->orderBy('name')->pluck('name', 'id')
                        : Profession::orderBy('name')->pluck('name', 'id');
                })
                ->searchable()
                ->preload()
                ->nullable()
                ->placeholder('All Professions'),
        ]);
    }
    
    public function getHeading(): string
    {
        $clientName = auth()->user()?->client?->name ?? 'Dashboard';
        return $clientName . ' / Dashboard';
    }

    public function getFooterWidgets(): array
    {
        return [
            // add footer widgets here if needed
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}