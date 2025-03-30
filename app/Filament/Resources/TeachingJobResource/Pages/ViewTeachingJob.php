<?php

namespace App\Filament\Resources\TeachingJobResource\Pages;

use App\Filament\Resources\TeachingJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeachingJob extends ViewRecord
{
    protected static string $resource = TeachingJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
