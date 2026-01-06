<?php

namespace App\Filament\Resources\AcademicPeriodResource\Pages;

use App\Filament\Resources\AcademicPeriodResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAcademicPeriods extends ListRecords
{
    protected static string $resource = AcademicPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
