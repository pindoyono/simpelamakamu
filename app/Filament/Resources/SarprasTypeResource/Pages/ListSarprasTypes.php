<?php

namespace App\Filament\Resources\SarprasTypeResource\Pages;

use App\Filament\Resources\SarprasTypeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSarprasTypes extends ListRecords
{
    protected static string $resource = SarprasTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
