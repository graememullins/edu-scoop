<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Models\NhsEnglandJob;
use App\Models\Profession;
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
                    NhsEnglandJob::query()
                        ->whereNotNull('region')
                        ->distinct()
                        ->orderBy('region')
                        ->pluck('region', 'region')
                )
                ->searchable()
                ->preload()
                ->placeholder('All Regions'),

            Select::make('profession_id')
                ->label('Filter by Profession')
                ->options(
                    Profession::query()
                        ->whereHas('keywords.jobs') // Only show professions actually in use
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
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