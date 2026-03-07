<?php

namespace App\Filament\Resources\StudentUniformResource\Pages;

use App\Filament\Resources\StudentUniformResource;
use App\Models\AcademicPeriod;
use App\Models\Sekolah;
use App\Models\StudentUniform;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecapStudentUniform extends Page
{
    protected static string $resource = StudentUniformResource::class;

    protected string $view = 'filament.resources.student-uniform.pages.recap';

    protected static ?string $title = 'Rekap Ukuran Seragam';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    public ?int $selectedSekolahId = null;
    public ?int $selectedPeriodId = null;

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
        $categories = ['ukuran_baju' => 'Baju', 'ukuran_celana_rok' => 'Celana/Rok'];

        $result = [];

        foreach ($categories as $column => $label) {
            $data = $this->getBaseQuery()
                ->select('kelas', $column . ' as size', DB::raw('COUNT(*) as total'))
                ->groupBy('kelas', $column)
                ->get();

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
                'label' => $label,
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
}
