<?php

namespace App\Filament\Resources\ProfessionResource\Pages;

use App\Filament\Resources\ProfessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfessions extends ListRecords
{
    protected static string $resource = ProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
