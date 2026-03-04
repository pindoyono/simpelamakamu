<?php

namespace App\Filament\Widgets;

use App\Models\AcademicPeriod;
use App\Models\SarprasCategory;
use App\Models\SekolahSarpras;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SekolahSarprasTableWidget extends Widget
{
    protected string $view = 'filament.widgets.sekolah-sarpras-table-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public ?int $sekolahId = null;
    public ?int $academicPeriodId = null;
    public string $semesterLabel = '';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('sekolah') && $user->sekolahs()->exists();
    }

    public function mount(): void
    {
        $user = Auth::user();
        $sekolah = $user?->sekolahs()?->first();

        if ($sekolah) {
            $this->sekolahId = $sekolah->id;
            $this->academicPeriodId = $sekolah->current_academic_period_id;

            if ($this->academicPeriodId) {
                $period = AcademicPeriod::find($this->academicPeriodId);
                $this->semesterLabel = $period ? $period->display_name : '';
            }
        }
    }

    protected function getViewData(): array
    {
        if (!$this->sekolahId) {
            return ['categories' => collect()];
        }

        $categories = SarprasCategory::where('is_active', true)
            ->with(['sarprasTypes' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Load existing sarpras data for this sekolah & period
        $sarprasData = SekolahSarpras::where('sekolah_id', $this->sekolahId)
            ->when($this->academicPeriodId, fn ($q) => $q->where('academic_period_id', $this->academicPeriodId))
            ->get()
            ->keyBy('sarpras_type_id');

        return [
            'categories' => $categories,
            'sarprasData' => $sarprasData,
            'semesterLabel' => $this->semesterLabel,
        ];
    }
}
