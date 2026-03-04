<?php

namespace App\Filament\Widgets;

use App\Models\Sekolah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SekolahInfoWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        // Info sekolah sekarang ditampilkan di SekolahMapWidget
        return false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $sekolah = $user?->sekolahs()?->first();

        if (!$sekolah) {
            return [];
        }

        return [
            Stat::make('Nama Sekolah', $sekolah->nama)
                ->description('NPSN: ' . ($sekolah->npsn ?? '-') . ' 🏫')
                ->color('primary')
                ->extraAttributes(['class' => 'text-sm']),

            Stat::make('Jenjang', strtoupper($sekolah->jenjang ?? '-'))
                ->description(($sekolah->status ?? '-') . ' 🎓')
                ->color('success')
                ->extraAttributes(['class' => 'text-sm']),

            Stat::make('Akreditasi', $sekolah->akreditasi ?? '-')
                ->description('Tahun berdiri: ' . ($sekolah->tahun_berdiri ?? '-') . ' ⭐')
                ->color('warning')
                ->extraAttributes(['class' => 'text-sm']),

            Stat::make('Kepala Sekolah', $sekolah->kepala_sekolah ?? '-')
                ->description('Pimpinan sekolah 👤')
                ->color('info')
                ->extraAttributes(['class' => 'text-sm']),

            Stat::make('Alamat', $sekolah->alamat ?? '-')
                ->description(($sekolah->kecamatan ?? '') . ($sekolah->kabupaten ? ', ' . $sekolah->kabupaten : '') . ' 📍')
                ->color('gray')
                ->extraAttributes(['class' => 'text-sm']),
        ];
    }

    protected function getColumns(): int
    {
        return 5;
    }
}
