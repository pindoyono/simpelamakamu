<?php

namespace App\Filament\Resources\StudentUniformResource\Pages;

use App\Filament\Resources\StudentUniformResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentUniform extends EditRecord
{
    protected static string $resource = StudentUniformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
