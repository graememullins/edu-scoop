<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Models\TeachingJob;
use App\Models\Profession;
use Filament\Forms\Components\DatePicker;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

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