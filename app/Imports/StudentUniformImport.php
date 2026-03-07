<?php

namespace App\Imports;

use App\Models\AcademicPeriod;
use App\Models\StudentUniform;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Reader\XLSX\Options as XlsxOptions;

class StudentUniformImport
{
    protected array $results = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    protected int $sekolahId;
    protected int $academicPeriodId;

    protected array $requiredColumns = [
        'nama_siswa',
        'jenis_kelamin',
        'kelas',
        'ukuran_baju',
        'ukuran_celana_rok',
        'ukuran_sepatu',
    ];

    protected array $columnMap = [
        'nama_siswa' => 'nama_siswa',
        'nama' => 'nama_siswa',
        'nisn' => 'nisn',
        'jenis_kelamin' => 'jenis_kelamin',
        'laki_laki_perempuan' => 'jenis_kelamin',
        'l_p' => 'jenis_kelamin',
        'gender' => 'jenis_kelamin',
        'kelas' => 'kelas',
        'ukuran_baju' => 'ukuran_baju',
        'baju' => 'ukuran_baju',
        'ukuran_celana_rok' => 'ukuran_celana_rok',
        'celana_rok' => 'ukuran_celana_rok',
        'celana' => 'ukuran_celana_rok',
        'rok' => 'ukuran_celana_rok',
        'ukuran_sepatu' => 'ukuran_sepatu',
        'sepatu' => 'ukuran_sepatu',
    ];

    protected array $validKelas = ['I', 'II', 'III', 'IV', 'V', 'VI'];
    protected array $validSizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

    public function __construct(int $sekolahId)
    {
        $this->sekolahId = $sekolahId;

        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        if (!$activePeriod) {
            throw new \RuntimeException('Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }
        $this->academicPeriodId = $activePeriod->id;
    }

    public function import(string $filePath): array
    {
        set_time_limit(0);

        $options = new XlsxOptions();
        $reader = new XlsxReader($options);
        $reader->open($filePath);

        $headers = [];
        $rowNumber = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowNumber++;
                $cells = $row->getCells();
                $values = array_map(fn($cell) => trim((string) $cell->getValue()), $cells);

                if ($rowNumber === 1) {
                    $headers = array_map(fn($v) => Str::snake(Str::lower(str_replace(['/', '-'], '_', $v))), $values);
                    $this->validateHeaders($headers);
                    continue;
                }

                if (empty(array_filter($values))) {
                    continue;
                }

                $rowData = array_combine($headers, array_pad($values, count($headers), ''));
                $this->processRow($rowData, $rowNumber);
            }
            break; // only first sheet
        }

        $reader->close();

        return $this->results;
    }

    protected function validateHeaders(array $headers): void
    {
        $mappedHeaders = [];
        foreach ($headers as $header) {
            if (isset($this->columnMap[$header])) {
                $mappedHeaders[] = $this->columnMap[$header];
            }
        }

        $missing = array_diff($this->requiredColumns, $mappedHeaders);

        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) .
                '. Pastikan file Excel memiliki kolom: Nama Siswa, Jenis Kelamin, Kelas, Ukuran Baju, Ukuran Celana/Rok, Ukuran Sepatu'
            );
        }
    }

    protected function processRow(array $rowData, int $rowNumber): void
    {
        try {
            $mapped = [];
            foreach ($rowData as $key => $value) {
                if (isset($this->columnMap[$key])) {
                    $mapped[$this->columnMap[$key]] = $value;
                }
            }

            // Validate required
            if (empty($mapped['nama_siswa'])) {
                $this->results['skipped']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Nama siswa kosong, dilewati.";
                return;
            }

            // Normalize jenis_kelamin
            $jk = Str::upper(trim($mapped['jenis_kelamin'] ?? ''));
            if (in_array($jk, ['L', 'LAKI-LAKI', 'LAKI_LAKI', 'LAKI LAKI', 'MALE'])) {
                $mapped['jenis_kelamin'] = 'L';
            } elseif (in_array($jk, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA'])) {
                $mapped['jenis_kelamin'] = 'P';
            } else {
                $this->results['failed']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Jenis kelamin '{$mapped['jenis_kelamin']}' tidak valid (gunakan L/P).";
                return;
            }

            // Normalize kelas
            $kelas = Str::upper(trim($mapped['kelas'] ?? ''));
            // Support numeric: 1-6
            $kelasMap = ['1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V', '6' => 'VI'];
            if (isset($kelasMap[$kelas])) {
                $kelas = $kelasMap[$kelas];
            }
            if (!in_array($kelas, $this->validKelas)) {
                $this->results['failed']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Kelas '{$mapped['kelas']}' tidak valid (gunakan I-VI).";
                return;
            }
            $mapped['kelas'] = $kelas;

            // Normalize ukuran baju
            $baju = Str::upper(trim($mapped['ukuran_baju'] ?? ''));
            if (!in_array($baju, $this->validSizes)) {
                $this->results['failed']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Ukuran baju '{$mapped['ukuran_baju']}' tidak valid (S/M/L/XL/XXL/XXXL).";
                return;
            }
            $mapped['ukuran_baju'] = $baju;

            // Normalize ukuran celana/rok
            $celana = Str::upper(trim($mapped['ukuran_celana_rok'] ?? ''));
            if (!in_array($celana, $this->validSizes)) {
                $this->results['failed']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Ukuran celana/rok '{$mapped['ukuran_celana_rok']}' tidak valid (S/M/L/XL/XXL/XXXL).";
                return;
            }
            $mapped['ukuran_celana_rok'] = $celana;

            // Normalize ukuran sepatu
            $sepatu = (int) trim($mapped['ukuran_sepatu'] ?? '0');
            if ($sepatu < 28 || $sepatu > 44) {
                $this->results['failed']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Ukuran sepatu '{$mapped['ukuran_sepatu']}' tidak valid (28-44).";
                return;
            }
            $mapped['ukuran_sepatu'] = $sepatu;

            // Check duplicate NISN
            $nisn = !empty($mapped['nisn']) ? trim($mapped['nisn']) : null;
            if ($nisn) {
                $exists = StudentUniform::where('nisn', $nisn)
                    ->where('academic_period_id', $this->academicPeriodId)
                    ->exists();
                if ($exists) {
                    $this->results['skipped']++;
                    $this->results['errors'][] = "Baris {$rowNumber}: NISN '{$nisn}' sudah terdaftar pada tahun ajaran ini, dilewati.";
                    return;
                }
            }

            StudentUniform::create([
                'sekolah_id' => $this->sekolahId,
                'academic_period_id' => $this->academicPeriodId,
                'nama_siswa' => $mapped['nama_siswa'],
                'nisn' => $mapped['nisn'] ?? null,
                'jenis_kelamin' => $mapped['jenis_kelamin'],
                'kelas' => $mapped['kelas'],
                'ukuran_baju' => $mapped['ukuran_baju'],
                'ukuran_celana_rok' => $mapped['ukuran_celana_rok'],
                'ukuran_sepatu' => $mapped['ukuran_sepatu'],
            ]);

            $this->results['success']++;

        } catch (\Exception $e) {
            $this->results['failed']++;
            $this->results['errors'][] = "Baris {$rowNumber}: " . $e->getMessage();
            Log::error("StudentUniformImport error row {$rowNumber}", ['error' => $e->getMessage()]);
        }
    }
}
