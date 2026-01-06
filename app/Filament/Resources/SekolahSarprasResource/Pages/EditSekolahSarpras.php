<?php

namespace App\Filament\Resources\SekolahSarprasResource\Pages;

use App\Filament\Resources\SekolahSarprasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSekolahSarpras extends EditRecord
{
    protected static string $resource = SekolahSarprasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
