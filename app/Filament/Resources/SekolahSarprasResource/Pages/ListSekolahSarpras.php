<?php

namespace App\Filament\Resources\SekolahSarprasResource\Pages;

use App\Filament\Resources\SekolahSarprasResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSekolahSarpras extends ListRecords
{
    protected static string $resource = SekolahSarprasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
