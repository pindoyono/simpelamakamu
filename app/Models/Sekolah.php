<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'npsn',
        'nama',
        'jenjang',
        'status',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'tahun_berdiri',
        'akreditasi',
        'logo',
        'is_active',
        'latitude',
        'longitude',
        'jumlah_guru',
        'jumlah_tu',
        'jumlah_siswa',
        'status_tanah',
        'kondisi_bangunan_umum',
        'current_academic_period_id',
    ];

    protected $casts = [
        'tahun_berdiri' => 'integer',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'jumlah_guru' => 'integer',
        'jumlah_tu' => 'integer',
        'jumlah_siswa' => 'integer',
    ];

    /**
     * Get the current academic period.
     */
    public function currentAcademicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class, 'current_academic_period_id');
    }

    /**
     * Get the sekolah sarpras records.
     */
    public function sekolahSarpras(): HasMany
    {
        return $this->hasMany(SekolahSarpras::class);
    }

    /**
     * Get the procurement proposals for this sekolah.
     */
    public function procurementProposals(): HasMany
    {
        return $this->hasMany(ProcurementProposal::class);
    }

    /**
     * Get the archives for this sekolah.
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    /**
     * Get the users associated with this sekolah.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sekolah_user');
    }

    /**
     * Get total staff count.
     */
    public function getTotalStaffAttribute(): int
    {
        return $this->jumlah_guru + $this->jumlah_tu;
    }
}
