<?php

namespace App\Imports;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Reader\XLSX\Options as XlsxOptions;

class SekolahImport
{
    protected array $results = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'errors' => [],
        'created_accounts' => [],
    ];

    /**
     * Required columns in the Excel file.
     */
    protected array $requiredColumns = [
        'npsn',
        'nama',
    ];

    /**
     * All supported columns mapped to Sekolah model fields.
     */
    protected array $columnMap = [
        'npsn' => 'npsn',
        'nama' => 'nama',
        'nama_sekolah' => 'nama',
        'jenjang' => 'jenjang',
        'status' => 'status',
        'alamat' => 'alamat',
        'kelurahan' => 'kelurahan',
        'kecamatan' => 'kecamatan',
        'kabupaten' => 'kabupaten',
        'provinsi' => 'provinsi',
        'kode_pos' => 'kode_pos',
        'telepon' => 'telepon',
        'email' => 'email',
        'email_sekolah' => 'email',
        'website' => 'website',
        'kepala_sekolah' => 'kepala_sekolah',
        'nip_kepala_sekolah' => 'nip_kepala_sekolah',
        'tahun_berdiri' => 'tahun_berdiri',
        'akreditasi' => 'akreditasi',
        'jumlah_guru' => 'jumlah_guru',
        'jumlah_tu' => 'jumlah_tu',
        'jumlah_siswa' => 'jumlah_siswa',
        'status_tanah' => 'status_tanah',
        'kondisi_bangunan_umum' => 'kondisi_bangunan_umum',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
    ];

    /**
     * Import sekolah data from an Excel file.
     */
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

                // First row = headers
                if ($rowNumber === 1) {
                    $headers = array_map(fn($v) => Str::snake(Str::lower($v)), $values);
                    $this->validateHeaders($headers);
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($values))) {
                    continue;
                }

                $rowData = array_combine($headers, array_pad($values, count($headers), ''));

                $this->processRow($rowData, $rowNumber);
            }

            // Only process first sheet
            break;
        }

        $reader->close();

        return $this->results;
    }

    /**
     * Validate that required headers exist.
     */
    protected function validateHeaders(array $headers): void
    {
        $missing = [];
        foreach ($this->requiredColumns as $required) {
            // Check direct match or mapped alias
            $found = false;
            foreach ($headers as $header) {
                if ($header === $required || ($this->columnMap[$header] ?? null) === $required) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missing[] = $required;
            }
        }

        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '. Pastikan file Excel memiliki kolom: npsn, nama'
            );
        }
    }

    /**
     * Process a single row from the Excel file.
     */
    protected function processRow(array $rowData, int $rowNumber): void
    {
        try {
            // Map Excel columns to model fields
            $sekolahData = $this->mapRowToSekolahData($rowData);

            // Validate required fields
            if (empty($sekolahData['npsn'])) {
                $this->results['skipped']++;
                $this->results['errors'][] = "Baris {$rowNumber}: NPSN kosong, baris dilewati.";
                return;
            }

            if (empty($sekolahData['nama'])) {
                $this->results['skipped']++;
                $this->results['errors'][] = "Baris {$rowNumber}: Nama sekolah kosong, baris dilewati.";
                return;
            }

            // Validate jenjang if provided
            $validJenjang = ['SD', 'SMP', 'SMA', 'SMK'];
            if (!empty($sekolahData['jenjang']) && !in_array(strtoupper($sekolahData['jenjang']), $validJenjang)) {
                $sekolahData['jenjang'] = 'SD'; // Default
            } else if (!empty($sekolahData['jenjang'])) {
                $sekolahData['jenjang'] = strtoupper($sekolahData['jenjang']);
            }

            // Validate status if provided
            $validStatus = ['Negeri', 'Swasta'];
            if (!empty($sekolahData['status'])) {
                $sekolahData['status'] = ucfirst(strtolower($sekolahData['status']));
                if (!in_array($sekolahData['status'], $validStatus)) {
                    $sekolahData['status'] = 'Negeri'; // Default
                }
            }

            // Cast numeric fields
            foreach (['tahun_berdiri', 'jumlah_guru', 'jumlah_tu', 'jumlah_siswa'] as $numericField) {
                if (isset($sekolahData[$numericField]) && $sekolahData[$numericField] !== '') {
                    $sekolahData[$numericField] = (int) $sekolahData[$numericField];
                } else {
                    unset($sekolahData[$numericField]);
                }
            }

            // Cast decimal fields
            foreach (['latitude', 'longitude'] as $decimalField) {
                if (isset($sekolahData[$decimalField]) && $sekolahData[$decimalField] !== '') {
                    $sekolahData[$decimalField] = (float) $sekolahData[$decimalField];
                } else {
                    unset($sekolahData[$decimalField]);
                }
            }

            // Remove empty string values
            $sekolahData = array_filter($sekolahData, fn($v) => $v !== '' && $v !== null);

            DB::beginTransaction();

            // Create or update Sekolah
            $sekolah = Sekolah::updateOrCreate(
                ['npsn' => $sekolahData['npsn']],
                $sekolahData
            );

            // Create user account for this sekolah
            $accountInfo = $this->createUserAccount($sekolah, $rowNumber);

            DB::commit();

            $this->results['success']++;

            if ($accountInfo) {
                $this->results['created_accounts'][] = $accountInfo;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->results['failed']++;
            $this->results['errors'][] = "Baris {$rowNumber}: " . $e->getMessage();
            Log::error("Import Sekolah Error - Baris {$rowNumber}", [
                'error' => $e->getMessage(),
                'data' => $rowData,
            ]);
        }
    }

    /**
     * Map raw row data to Sekolah model fields.
     */
    protected function mapRowToSekolahData(array $rowData): array
    {
        $mapped = [];

        foreach ($rowData as $column => $value) {
            $column = Str::snake(Str::lower(trim($column)));

            if (isset($this->columnMap[$column])) {
                $modelField = $this->columnMap[$column];
                // Don't overwrite if already set by a more specific column
                if (!isset($mapped[$modelField]) || $mapped[$modelField] === '') {
                    $mapped[$modelField] = $value;
                }
            }
        }

        return $mapped;
    }

    /**
     * Create a user account for the sekolah.
     * Username: NPSN
     * Password: NPSN (default, should be changed on first login)
     */
    protected function createUserAccount(Sekolah $sekolah, int $rowNumber): ?array
    {
        $npsn = $sekolah->npsn;
        $email = $sekolah->email ?: $npsn . '@simpelsapakamu.id';

        // Check if user with this email already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Ensure the user is linked to this sekolah
            if (!$existingUser->sekolahs()->where('sekolahs.id', $sekolah->id)->exists()) {
                $existingUser->sekolahs()->attach($sekolah->id);
            }

            // Ensure sekolah role is assigned
            if (!$existingUser->hasRole('sekolah')) {
                $existingUser->assignRole('sekolah');
            }

            return null; // Account already exists, no new account info to report
        }

        // Create new user account
        $password = $npsn; // Default password = NPSN

        $user = User::create([
            'name' => $sekolah->nama,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Assign 'sekolah' role
        $user->assignRole('sekolah');

        // Link user to sekolah
        $user->sekolahs()->attach($sekolah->id);

        return [
            'npsn' => $npsn,
            'nama' => $sekolah->nama,
            'email' => $email,
            'password' => $password,
        ];
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
