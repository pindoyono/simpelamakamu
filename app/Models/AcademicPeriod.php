<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sekolahs using this academic period as current.
     */
    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class, 'current_academic_period_id');
    }

    /**
     * Get the sekolah sarpras for this period.
     */
    public function sekolahSarpras(): HasMany
    {
        return $this->hasMany(SekolahSarpras::class);
    }

    /**
     * Get the procurement proposals for this period.
     */
    public function procurementProposals(): HasMany
    {
        return $this->hasMany(ProcurementProposal::class);
    }

    /**
     * Get the archives for this period.
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    /**
     * Get the display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->year . ' - ' . ucfirst($this->semester);
    }
}
