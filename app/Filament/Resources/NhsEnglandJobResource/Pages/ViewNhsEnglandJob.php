<?php

namespace App\Filament\Resources\NhsEnglandJobResource\Pages;

use App\Filament\Resources\NhsEnglandJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNhsEnglandJob extends ViewRecord
{
    protected static string $resource = NhsEnglandJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
