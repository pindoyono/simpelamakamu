<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SekolahSarpras extends Model
{
    use HasFactory;

    protected $table = 'sekolah_sarpras';

    protected $fillable = [
        'sekolah_id',
        'sarpras_type_id',
        'academic_period_id',
        'jumlah',
        'kondisi_baik',
        'kondisi_rusak_ringan',
        'kondisi_rusak_sedang',
        'kondisi_rusak_berat',
        'nilai_perolehan',
        'tahun_perolehan',
        'keterangan',
        'data_source',
        'is_verified',
        'verified_by',
        'verified_at',
        'created_by',
        'needs_attention',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'kondisi_baik' => 'integer',
        'kondisi_rusak_ringan' => 'integer',
        'kondisi_rusak_sedang' => 'integer',
        'kondisi_rusak_berat' => 'integer',
        'nilai_perolehan' => 'decimal:2',
        'tahun_perolehan' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'needs_attention' => 'boolean',
    ];

    /**
     * Get the sekolah for this sarpras.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Get the sarpras type for this record.
     */
    public function sarprasType(): BelongsTo
    {
        return $this->belongsTo(SarprasType::class);
    }

    /**
     * Get the academic period for this record.
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the user who verified this record.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate total items.
     */
    public function getTotalKondisiAttribute(): int
    {
        return $this->kondisi_baik + $this->kondisi_rusak_ringan +
               $this->kondisi_rusak_sedang + $this->kondisi_rusak_berat;
    }
}
