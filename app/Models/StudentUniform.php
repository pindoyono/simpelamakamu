<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentUniform extends Model
{
    use HasFactory;

    protected $table = 'student_uniforms';

    protected $fillable = [
        'sekolah_id',
        'academic_period_id',
        'nama_siswa',
        'nisn',
        'jenis_kelamin',
        'kelas',
        'ukuran_baju',
        'ukuran_celana_rok',
        'ukuran_sepatu',
    ];

    protected $casts = [
        'ukuran_sepatu' => 'integer',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public static function getKelasOptions(): array
    {
        return [
            'I' => 'I',
            'II' => 'II',
            'III' => 'III',
            'IV' => 'IV',
            'V' => 'V',
            'VI' => 'VI',
        ];
    }

    public static function getSizeOptions(): array
    {
        return [
            'S' => 'S',
            'M' => 'M',
            'L' => 'L',
            'XL' => 'XL',
            'XXL' => 'XXL',
            'XXXL' => 'XXXL',
        ];
    }

    public static function getSepatuSizeOptions(): array
    {
        $sizes = [];
        for ($i = 28; $i <= 44; $i++) {
            $sizes[$i] = (string) $i;
        }
        return $sizes;
    }
}
