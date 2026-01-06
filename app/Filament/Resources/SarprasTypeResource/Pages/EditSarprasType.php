<?php

namespace App\Filament\Resources\SarprasTypeResource\Pages;

use App\Filament\Resources\SarprasTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSarprasType extends EditRecord
{
    protected static string $resource = SarprasTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
