<?php

namespace App\Filament\Resources\ProfessionGroupResource\Pages;

use App\Filament\Resources\ProfessionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfessionGroups extends ListRecords
{
    protected static string $resource = ProfessionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
