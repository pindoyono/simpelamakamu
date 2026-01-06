<?php

namespace App\Filament\Resources\SarprasCategoryResource\Pages;

use App\Filament\Resources\SarprasCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSarprasCategories extends ListRecords
{
    protected static string $resource = SarprasCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
