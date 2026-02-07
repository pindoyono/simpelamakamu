<?php

namespace App\Filament\Widgets;

use App\Models\Sekolah;
use App\Models\SekolahSarpras;
use App\Models\ProcurementProposal;
use App\Models\SarprasCategory;
use App\Models\AcademicPeriod;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SisarprasStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = Auth::user();

        // Hide for users with only 'sekolah' role
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            return false;
        }

        return true;
    }

    protected function getStats(): array
    {
        $totalSekolah = Sekolah::count();
        $totalSarpras = SekolahSarpras::count();
        $totalProposals = ProcurementProposal::count();
        $pendingProposals = ProcurementProposal::where('status', 'submitted')->count();

        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $periodLabel = $activePeriod ? $activePeriod->year . ' - ' . ucfirst($activePeriod->semester) : 'Belum ada';

        // Calculate kondisi statistics
        $kondisiBaik = SekolahSarpras::sum('kondisi_baik');
        $kondisiRusak = SekolahSarpras::sum('kondisi_rusak_ringan') +
                        SekolahSarpras::sum('kondisi_rusak_sedang') +
                        SekolahSarpras::sum('kondisi_rusak_berat');

        $totalKondisi = $kondisiBaik + $kondisiRusak;
        $percentBaik = $totalKondisi > 0 ? round(($kondisiBaik / $totalKondisi) * 100, 1) : 0;

        return [
            Stat::make('Total Sekolah', $totalSekolah)
                ->description('Sekolah terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Data SARPRAS', $totalSarpras)
                ->description('Record inventaris')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('success'),

            Stat::make('Kondisi Baik', $percentBaik . '%')
                ->description($kondisiBaik . ' dari ' . $totalKondisi . ' item')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($percentBaik >= 70 ? 'success' : ($percentBaik >= 50 ? 'warning' : 'danger')),

            Stat::make('Usulan Pengadaan', $totalProposals)
                ->description($pendingProposals . ' menunggu review')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($pendingProposals > 0 ? 'warning' : 'info'),

            Stat::make('Periode Aktif', $periodLabel)
                ->description('Tahun ajaran saat ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
