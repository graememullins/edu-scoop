<?php

namespace App\Filament\Resources\ProfessionGroupResource\Pages;

use App\Filament\Resources\ProfessionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProfessionGroup extends EditRecord
{
    protected static string $resource = ProfessionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
