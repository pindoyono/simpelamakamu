<?php

namespace App\Filament\Resources\SarprasCategoryResource\Pages;

use App\Filament\Resources\SarprasCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSarprasCategory extends EditRecord
{
    protected static string $resource = SarprasCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
