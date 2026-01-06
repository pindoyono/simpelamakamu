<?php

namespace App\Filament\Resources\ProcurementProposalResource\Pages;

use App\Filament\Resources\ProcurementProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcurementProposal extends EditRecord
{
    protected static string $resource = ProcurementProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
