<?php

namespace App\Filament\Resources\StudentUniformResource\Pages;

use App\Filament\Resources\StudentUniformResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListStudentUniforms extends ListRecords
{
    protected static string $resource = StudentUniformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recap')
                ->label('Rekap Ukuran')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn () => StudentUniformResource::getUrl('recap')),
            Actions\CreateAction::make()
                ->label('')
                ->icon('heroicon-o-plus'),
        ];
    }
}
