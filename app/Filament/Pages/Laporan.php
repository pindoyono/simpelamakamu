<?php

namespace App\Filament\Pages;

use App\Models\Sekolah;
use App\Models\SekolahSarpras;
use App\Models\ProcurementProposal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Laporan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan Rekap Data Sekolah';

    protected static ?string $slug = 'laporan';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.laporan';

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('admin'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Download Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(fn () => $this->exportToExcel()),
        ];
    }

    public function exportToExcel(): StreamedResponse
    {
        $rekapData = $this->getRekapData();

        return response()->streamDownload(function () use ($rekapData) {
            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile('php://output');

            // ===== Styles =====
            $titleStyle = (new Style())
                ->setFontBold()
                ->setFontSize(14)
                ->setCellAlignment(CellAlignment::CENTER);

            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(10)
                ->setFontColor(Color::WHITE)
                ->setBackgroundColor('2563EB')
                ->setCellAlignment(CellAlignment::CENTER);

            $subHeaderStyle = (new Style())
                ->setFontBold()
                ->setFontSize(9)
                ->setFontColor(Color::WHITE)
                ->setBackgroundColor('3B82F6')
                ->setCellAlignment(CellAlignment::CENTER);

            $dataStyle = (new Style())
                ->setFontSize(10)
                ->setCellAlignment(CellAlignment::CENTER);

            $dataLeftStyle = (new Style())
                ->setFontSize(10)
                ->setCellAlignment(CellAlignment::LEFT);

            $totalStyle = (new Style())
                ->setFontBold()
                ->setFontSize(10)
                ->setBackgroundColor('E5E7EB')
                ->setCellAlignment(CellAlignment::CENTER);

            // ===== Title Row =====
            $writer->addRow(Row::fromValues(
                ['LAPORAN REKAP DATA SEKOLAH'],
                $titleStyle
            ));
            $writer->addRow(Row::fromValues(
                ['Tanggal: ' . now()->format('d F Y')],
                (new Style())->setFontSize(10)->setCellAlignment(CellAlignment::LEFT)
            ));
            $writer->addRow(Row::fromValues([''])); // Empty row

            // ===== Summary =====
            $totalSekolah = count($rekapData);
            $totalSiswa = array_sum(array_column($rekapData, 'jumlah_siswa'));
            $totalGuru = array_sum(array_column($rekapData, 'jumlah_guru'));
            $totalTu = array_sum(array_column($rekapData, 'jumlah_tu'));
            $totalSarpras = array_sum(array_column($rekapData, 'total_jumlah_sarpras'));
            $totalProposal = array_sum(array_column($rekapData, 'total_proposal'));

            $summaryHeaderStyle = (new Style())
                ->setFontBold()
                ->setFontSize(10)
                ->setBackgroundColor('DBEAFE')
                ->setCellAlignment(CellAlignment::CENTER);

            $summaryValueStyle = (new Style())
                ->setFontBold()
                ->setFontSize(12)
                ->setCellAlignment(CellAlignment::CENTER);

            $writer->addRow(Row::fromValues(
                ['Total Sekolah', 'Total Siswa', 'Total Guru', 'Total Sarpras', 'Total Rehabilitasi'],
                $summaryHeaderStyle
            ));
            $writer->addRow(Row::fromValues(
                [$totalSekolah, $totalSiswa, $totalGuru, $totalSarpras, $totalProposal],
                $summaryValueStyle
            ));
            $writer->addRow(Row::fromValues([''])); // Empty row

            // ===== Table Header Row 1 =====
            $writer->addRow(Row::fromValues(
                ['No', 'Sekolah', 'NPSN', 'Jenjang', 'Siswa', 'Guru', 'TU', 'Jml Sarpras', 'Kondisi Baik', 'Kondisi Rusak', 'Total Rehab', 'Rehab Disetujui', 'Rehab Pending'],
                $headerStyle
            ));

            // ===== Data Rows =====
            foreach ($rekapData as $index => $data) {
                $writer->addRow(Row::fromValues([
                    $index + 1,
                    $data['sekolah']->nama,
                    $data['sekolah']->npsn,
                    $data['sekolah']->jenjang ?? '-',
                    $data['jumlah_siswa'],
                    $data['jumlah_guru'],
                    $data['jumlah_tu'],
                    $data['total_jumlah_sarpras'],
                    $data['kondisi_baik'],
                    $data['kondisi_rusak'],
                    $data['total_proposal'],
                    $data['proposal_approved'],
                    $data['proposal_pending'],
                ], $dataStyle));
            }

            // ===== Total Row =====
            $writer->addRow(Row::fromValues([
                '',
                'TOTAL',
                '',
                '',
                $totalSiswa,
                $totalGuru,
                $totalTu,
                $totalSarpras,
                array_sum(array_column($rekapData, 'kondisi_baik')),
                array_sum(array_column($rekapData, 'kondisi_rusak')),
                $totalProposal,
                array_sum(array_column($rekapData, 'proposal_approved')),
                array_sum(array_column($rekapData, 'proposal_pending')),
            ], $totalStyle));

            $writer->close();
        }, 'laporan-rekap-sekolah-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getRekapData(): array
    {
        $sekolahs = Sekolah::with(['currentAcademicPeriod'])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $rekapData = [];

        foreach ($sekolahs as $sekolah) {
            $sarprasQuery = SekolahSarpras::where('sekolah_id', $sekolah->id);

            if ($sekolah->current_academic_period_id) {
                $sarprasQuery->where('academic_period_id', $sekolah->current_academic_period_id);
            }

            $sarprasStats = $sarprasQuery->selectRaw('
                COUNT(*) as total_item,
                COALESCE(SUM(jumlah), 0) as total_jumlah,
                COALESCE(SUM(kondisi_baik), 0) as total_baik,
                COALESCE(SUM(kondisi_rusak_ringan), 0) as total_rusak_ringan,
                COALESCE(SUM(kondisi_rusak_sedang), 0) as total_rusak_sedang,
                COALESCE(SUM(kondisi_rusak_berat), 0) as total_rusak_berat
            ')->first();

            $proposalCount = ProcurementProposal::where('sekolah_id', $sekolah->id)->count();
            $proposalApproved = ProcurementProposal::where('sekolah_id', $sekolah->id)
                ->where('status', 'approved')->count();
            $proposalPending = ProcurementProposal::where('sekolah_id', $sekolah->id)
                ->whereIn('status', ['submitted', 'under_review'])->count();

            $rekapData[] = [
                'sekolah' => $sekolah,
                'jumlah_siswa' => $sekolah->jumlah_siswa ?? 0,
                'jumlah_guru' => $sekolah->jumlah_guru ?? 0,
                'jumlah_tu' => $sekolah->jumlah_tu ?? 0,
                'total_item_sarpras' => $sarprasStats->total_item ?? 0,
                'total_jumlah_sarpras' => $sarprasStats->total_jumlah ?? 0,
                'kondisi_baik' => $sarprasStats->total_baik ?? 0,
                'kondisi_rusak' => ($sarprasStats->total_rusak_ringan ?? 0)
                    + ($sarprasStats->total_rusak_sedang ?? 0)
                    + ($sarprasStats->total_rusak_berat ?? 0),
                'total_proposal' => $proposalCount,
                'proposal_approved' => $proposalApproved,
                'proposal_pending' => $proposalPending,
            ];
        }

        return $rekapData;
    }

    protected function getViewData(): array
    {
        $rekapData = $this->getRekapData();

        $totalSekolah = count($rekapData);
        $totalSiswa = array_sum(array_column($rekapData, 'jumlah_siswa'));
        $totalGuru = array_sum(array_column($rekapData, 'jumlah_guru'));
        $totalSarpras = array_sum(array_column($rekapData, 'total_jumlah_sarpras'));
        $totalProposal = array_sum(array_column($rekapData, 'total_proposal'));

        return [
            'rekapData' => $rekapData,
            'totalSekolah' => $totalSekolah,
            'totalSiswa' => $totalSiswa,
            'totalGuru' => $totalGuru,
            'totalSarpras' => $totalSarpras,
            'totalProposal' => $totalProposal,
        ];
    }
}
