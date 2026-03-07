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
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->url(fn () => StudentUniformResource::getUrl('import')),
            Actions\Action::make('recap')
                ->label('Rekap Ukuran')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn () => \App\Filament\Pages\RekapSeragam::getUrl()),
            Actions\CreateAction::make()
                ->label('')
                ->icon('heroicon-o-plus'),
        ];
    }
}
