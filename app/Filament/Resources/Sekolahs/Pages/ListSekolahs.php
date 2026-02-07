<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListSekolahs extends ListRecords
{
    protected static string $resource = SekolahResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        // Hide create button for sekolah role
        if ($user && $user->hasRole('sekolah')) {
            return [];
        }

        return [
            CreateAction::make()
                ->label('')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        $user = Auth::user();

        // Filter untuk role sekolah - hanya tampilkan sekolah miliknya
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            $sekolahIds = $user->sekolahs()->pluck('sekolahs.id')->toArray();
            $query->whereIn('id', $sekolahIds);
        }

        return $query;
    }
}
