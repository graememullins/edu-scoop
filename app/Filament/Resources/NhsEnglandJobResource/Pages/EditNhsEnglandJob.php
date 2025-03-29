<?php

namespace App\Filament\Resources\NhsEnglandJobResource\Pages;

use App\Filament\Resources\NhsEnglandJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNhsEnglandJob extends EditRecord
{
    protected static string $resource = NhsEnglandJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
