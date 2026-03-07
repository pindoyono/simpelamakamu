<?php

namespace App\Filament\Pages;

use App\Models\AcademicPeriod;
use App\Models\Sekolah;
use App\Models\StudentUniform;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapSeragam extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Rekap Seragam';

    protected static ?string $title = 'Rekap Ukuran Seragam';

    protected static ?string $slug = 'rekap-seragam';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.rekap-seragam';

    public ?int $selectedSekolahId = null;
    public ?int $selectedPeriodId = null;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('sekolah'));
    }

    public function mount(): void
    {
        $user = Auth::user();
        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');

        if ($isSekolahRole) {
            $this->selectedSekolahId = $user->sekolahs()->first()?->id;
        }

        $this->selectedPeriodId = AcademicPeriod::where('is_active', true)->first()?->id;
    }

    public function getSekolahOptionsProperty(): array
    {
        return Sekolah::where('is_active', true)->orderBy('nama')->pluck('nama', 'id')->toArray();
    }

    public function getPeriodOptionsProperty(): array
    {
        return AcademicPeriod::orderBy('year', 'desc')
            ->get()
            ->mapWithKeys(fn ($p) => [$p->id => $p->year . ($p->is_active ? ' ✓' : '')])
            ->toArray();
    }

    public function getSelectedSekolahNameProperty(): string
    {
        if ($this->selectedSekolahId) {
            return Sekolah::find($this->selectedSekolahId)?->nama ?? '-';
        }
        return '-';
    }

    public function getIsSekolahRoleProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
    }

    protected function getHeaderActions(): array
    {
        $isAdmin = !$this->isSekolahRole;

        return [
            Action::make('exportExcel')
                ->label('Download Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => $this->selectedSekolahId !== null),
            Action::make('exportAllExcel')
                ->label('Download Semua Sekolah')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('info')
                ->action(fn () => $this->exportAllToExcel())
                ->visible(fn () => $isAdmin),
        ];
    }

    protected function getBaseQuery()
    {
        $query = StudentUniform::query();

        if ($this->selectedSekolahId) {
            $query->where('sekolah_id', $this->selectedSekolahId);
        }

        if ($this->selectedPeriodId) {
            $query->where('academic_period_id', $this->selectedPeriodId);
        }

        return $query;
    }

    public function getPakaianRecapProperty(): array
    {
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        $kelasList = ['I', 'II', 'III', 'IV', 'V', 'VI'];

        // Baju: all students
        // Celana: male students (L)
        // Rok: female students (P)
        $categories = [
            ['column' => 'ukuran_baju', 'label' => 'Baju', 'gender' => null],
            ['column' => 'ukuran_celana_rok', 'label' => 'Celana', 'gender' => 'L'],
            ['column' => 'ukuran_celana_rok', 'label' => 'Rok', 'gender' => 'P'],
        ];

        $result = [];

        foreach ($categories as $cat) {
            $query = $this->getBaseQuery()
                ->select('kelas', $cat['column'] . ' as size', DB::raw('COUNT(*) as total'))
                ->groupBy('kelas', $cat['column']);

            if ($cat['gender']) {
                $query->where('jenis_kelamin', $cat['gender']);
            }

            $data = $query->get();

            $matrix = [];
            $totals = array_fill_keys($sizes, 0);

            foreach ($kelasList as $kelas) {
                $row = array_fill_keys($sizes, 0);
                foreach ($data->where('kelas', $kelas) as $item) {
                    $row[$item->size] = $item->total;
                    $totals[$item->size] += $item->total;
                }
                $row['total_row'] = array_sum($row);
                $matrix[$kelas] = $row;
            }

            $totals['total_row'] = array_sum($totals);
            $matrix['Jumlah'] = $totals;

            $result[] = [
                'label' => $cat['label'],
                'sizes' => $sizes,
                'matrix' => $matrix,
            ];
        }

        return $result;
    }

    public function getSepatuRecapProperty(): array
    {
        $sizes = range(28, 44);
        $kelasList = ['I', 'II', 'III', 'IV', 'V', 'VI'];

        $data = $this->getBaseQuery()
            ->select('kelas', 'ukuran_sepatu as size', DB::raw('COUNT(*) as total'))
            ->groupBy('kelas', 'ukuran_sepatu')
            ->get();

        $matrix = [];
        $totals = array_fill_keys($sizes, 0);

        foreach ($kelasList as $kelas) {
            $row = array_fill_keys($sizes, 0);
            foreach ($data->where('kelas', $kelas) as $item) {
                $s = (int) $item->size;
                if (isset($row[$s])) {
                    $row[$s] = $item->total;
                    $totals[$s] += $item->total;
                }
            }
            $row['total_row'] = array_sum($row);
            $matrix[$kelas] = $row;
        }

        $totals['total_row'] = array_sum($totals);
        $matrix['Jumlah'] = $totals;

        return [
            'sizes' => $sizes,
            'matrix' => $matrix,
        ];
    }

    public function getTotalSiswaProperty(): int
    {
        return $this->getBaseQuery()->count();
    }

    public function exportToExcel(): StreamedResponse
    {
        $sekolahName = $this->selectedSekolahName;
        $periodId = $this->selectedPeriodId;
        $sekolahId = $this->selectedSekolahId;
        $periodName = $periodId ? AcademicPeriod::find($periodId)?->year ?? '' : '';

        return response()->streamDownload(function () use ($sekolahName, $periodName, $sekolahId, $periodId) {
            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile('php://output');

            $pakaianRecap = $this->buildPakaianRecap($sekolahId, $periodId);
            $sepatuRecap = $this->buildSepatuRecap($sekolahId, $periodId);

            $this->writeSheetContent($writer, $sekolahName, $periodName, $pakaianRecap, $sepatuRecap);

            $writer->close();
        }, 'rekap-seragam-' . str_replace(' ', '-', strtolower($sekolahName)) . '-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportAllToExcel(): StreamedResponse
    {
        $periodId = $this->selectedPeriodId;
        $periodName = $periodId ? AcademicPeriod::find($periodId)?->year ?? '' : '';

        // Get all schools that have uniform data
        $query = StudentUniform::query();
        if ($periodId) {
            $query->where('academic_period_id', $periodId);
        }
        $sekolahIds = $query->distinct()->pluck('sekolah_id')->toArray();
        $sekolahs = Sekolah::whereIn('id', $sekolahIds)->orderBy('nama')->get();

        return response()->streamDownload(function () use ($sekolahs, $periodId, $periodName) {
            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile('php://output');

            $isFirst = true;
            foreach ($sekolahs as $sekolah) {
                $sheetName = mb_substr(str_replace(['/', '\\', '?', '*', '[', ']', ':'], '-', $sekolah->nama), 0, 31);

                if ($isFirst) {
                    $writer->getCurrentSheet()->setName($sheetName);
                    $isFirst = false;
                } else {
                    $newSheet = $writer->addNewSheetAndMakeItCurrent();
                    $newSheet->setName($sheetName);
                }

                $pakaianRecap = $this->buildPakaianRecap($sekolah->id, $periodId);
                $sepatuRecap = $this->buildSepatuRecap($sekolah->id, $periodId);

                $this->writeSheetContent($writer, $sekolah->nama, $periodName, $pakaianRecap, $sepatuRecap);
            }

            if ($sekolahs->isEmpty()) {
                $writer->addRow(Row::fromValues(['Tidak ada data seragam siswa']));
            }

            $writer->close();
        }, 'rekap-seragam-semua-sekolah-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function buildPakaianRecap(?int $sekolahId, ?int $periodId): array
    {
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        $kelasList = ['I', 'II', 'III', 'IV', 'V', 'VI'];

        $categories = [
            ['column' => 'ukuran_baju', 'label' => 'Baju', 'gender' => null],
            ['column' => 'ukuran_celana_rok', 'label' => 'Celana', 'gender' => 'L'],
            ['column' => 'ukuran_celana_rok', 'label' => 'Rok', 'gender' => 'P'],
        ];

        $result = [];

        foreach ($categories as $cat) {
            $query = StudentUniform::query();
            if ($sekolahId) {
                $query->where('sekolah_id', $sekolahId);
            }
            if ($periodId) {
                $query->where('academic_period_id', $periodId);
            }

            $query->select('kelas', $cat['column'] . ' as size', DB::raw('COUNT(*) as total'))
                ->groupBy('kelas', $cat['column']);

            if ($cat['gender']) {
                $query->where('jenis_kelamin', $cat['gender']);
            }

            $data = $query->get();

            $matrix = [];
            $totals = array_fill_keys($sizes, 0);

            foreach ($kelasList as $kelas) {
                $row = array_fill_keys($sizes, 0);
                foreach ($data->where('kelas', $kelas) as $item) {
                    $row[$item->size] = $item->total;
                    $totals[$item->size] += $item->total;
                }
                $row['total_row'] = array_sum($row);
                $matrix[$kelas] = $row;
            }

            $totals['total_row'] = array_sum($totals);
            $matrix['Jumlah'] = $totals;

            $result[] = [
                'label' => $cat['label'],
                'sizes' => $sizes,
                'matrix' => $matrix,
            ];
        }

        return $result;
    }

    protected function buildSepatuRecap(?int $sekolahId, ?int $periodId): array
    {
        $sizes = range(28, 44);
        $kelasList = ['I', 'II', 'III', 'IV', 'V', 'VI'];

        $query = StudentUniform::query();
        if ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        }
        if ($periodId) {
            $query->where('academic_period_id', $periodId);
        }

        $data = $query->select('kelas', 'ukuran_sepatu as size', DB::raw('COUNT(*) as total'))
            ->groupBy('kelas', 'ukuran_sepatu')
            ->get();

        $matrix = [];
        $totals = array_fill_keys($sizes, 0);

        foreach ($kelasList as $kelas) {
            $row = array_fill_keys($sizes, 0);
            foreach ($data->where('kelas', $kelas) as $item) {
                $s = (int) $item->size;
                if (isset($row[$s])) {
                    $row[$s] = $item->total;
                    $totals[$s] += $item->total;
                }
            }
            $row['total_row'] = array_sum($row);
            $matrix[$kelas] = $row;
        }

        $totals['total_row'] = array_sum($totals);
        $matrix['Jumlah'] = $totals;

        return [
            'sizes' => $sizes,
            'matrix' => $matrix,
        ];
    }

    protected function writeSheetContent(Writer $writer, string $sekolahName, string $periodName, array $pakaianRecap, array $sepatuRecap): void
    {
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        $kelasList = ['I', 'II', 'III', 'IV', 'V', 'VI'];

        // Styles
        $titleStyle = (new Style())->setFontBold()->setFontSize(11);
        $headerGroupStyle = (new Style())->setFontBold()->setFontSize(10)->setFontColor(Color::WHITE)->setBackgroundColor('2563EB')->setCellAlignment(CellAlignment::CENTER);
        $headerSubStyle = (new Style())->setFontBold()->setFontSize(9)->setFontColor(Color::WHITE)->setBackgroundColor('3B82F6')->setCellAlignment(CellAlignment::CENTER);
        $dataStyle = (new Style())->setFontSize(10)->setCellAlignment(CellAlignment::CENTER);
        $totalLabelStyle = (new Style())->setFontSize(10)->setFontBold()->setFontColor(Color::BLUE)->setCellAlignment(CellAlignment::CENTER);
        $sectionStyle = (new Style())->setFontBold()->setFontSize(11);

        // ===== Nama Sekolah =====
        $writer->addRow(Row::fromValues(['Nama Sekolah', ':', $sekolahName], $titleStyle));
        if ($periodName) {
            $writer->addRow(Row::fromValues(['Tahun Ajaran', ':', $periodName], $titleStyle));
        }
        $writer->addRow(Row::fromValues(['']));

        // ===== PAKAIAN SISWA =====
        $writer->addRow(Row::fromValues(['PAKAIAN SISWA (Seragam Putih Merah, Seragam Pramuka, Seragam Olah Raga, Seragam Batik)'], $sectionStyle));

        // Header row 1
        $headerRow1 = ['No', 'Kelas'];
        foreach ($pakaianRecap as $cat) {
            $headerRow1[] = $cat['label'];
            for ($i = 1; $i < count($sizes); $i++) {
                $headerRow1[] = '';
            }
        }
        $writer->addRow(Row::fromValues($headerRow1, $headerGroupStyle));

        // Header row 2
        $headerRow2 = ['', ''];
        foreach ($pakaianRecap as $cat) {
            foreach ($sizes as $size) {
                $headerRow2[] = $size;
            }
        }
        $writer->addRow(Row::fromValues($headerRow2, $headerSubStyle));

        // Data rows
        $no = 1;
        foreach ($kelasList as $kelas) {
            $dataRow = [$no++, $kelas];
            foreach ($pakaianRecap as $cat) {
                foreach ($sizes as $size) {
                    $val = $cat['matrix'][$kelas][$size] ?? 0;
                    $dataRow[] = $val > 0 ? $val : '';
                }
            }
            $writer->addRow(Row::fromValues($dataRow, $dataStyle));
        }

        // Jumlah row
        $totalRow = ['', 'Jumlah'];
        foreach ($pakaianRecap as $cat) {
            foreach ($sizes as $size) {
                $val = $cat['matrix']['Jumlah'][$size] ?? 0;
                $totalRow[] = $val > 0 ? $val : '';
            }
        }
        $writer->addRow(Row::fromValues($totalRow, $totalLabelStyle));

        $writer->addRow(Row::fromValues(['']));

        // ===== SEPATU =====
        $writer->addRow(Row::fromValues(['SEPATU'], $sectionStyle));

        $sepatuSizes = $sepatuRecap['sizes'];

        // Header row 1
        $shoeHeader1 = ['No', 'Kelas', 'SIZE / UKURAN'];
        for ($i = 1; $i < count($sepatuSizes); $i++) {
            $shoeHeader1[] = '';
        }
        $writer->addRow(Row::fromValues($shoeHeader1, $headerGroupStyle));

        // Header row 2
        $shoeHeader2 = ['', ''];
        foreach ($sepatuSizes as $size) {
            $shoeHeader2[] = $size;
        }
        $writer->addRow(Row::fromValues($shoeHeader2, $headerSubStyle));

        // Data rows
        $no = 1;
        foreach ($kelasList as $kelas) {
            $dataRow = [$no++, $kelas];
            foreach ($sepatuSizes as $size) {
                $val = $sepatuRecap['matrix'][$kelas][$size] ?? 0;
                $dataRow[] = $val > 0 ? $val : '';
            }
            $writer->addRow(Row::fromValues($dataRow, $dataStyle));
        }

        // Jumlah row
        $totalRow = ['', 'Jumlah'];
        foreach ($sepatuSizes as $size) {
            $val = $sepatuRecap['matrix']['Jumlah'][$size] ?? 0;
            $totalRow[] = $val > 0 ? $val : '';
        }
        $writer->addRow(Row::fromValues($totalRow, $totalLabelStyle));
    }
}
