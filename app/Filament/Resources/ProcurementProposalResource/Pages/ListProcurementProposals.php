<?php

namespace App\Filament\Resources\ProcurementProposalResource\Pages;

use App\Filament\Resources\ProcurementProposalResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListProcurementProposals extends ListRecords
{
    protected static string $resource = ProcurementProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('')
                ->icon('heroicon-o-plus'),
        ];
    }
}
