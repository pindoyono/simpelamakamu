<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Imports\SekolahImport;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportSekolah extends Page
{
    protected static string $resource = SekolahResource::class;

    protected string $view = 'filament.resources.sekolahs.pages.import-sekolah';

    protected static ?string $title = 'Import Data Sekolah';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public ?array $importResults = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('file')
                    ->label('File Excel (.xlsx)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->maxSize(10240) // 10MB
                    ->required()
                    ->storeFiles(false)
                    ->helperText('Upload file Excel (.xlsx) dengan kolom minimal: NPSN, Nama. Kolom opsional: Jenjang, Status, Alamat, Kelurahan, Kecamatan, Kabupaten, Provinsi, Kode Pos, Telepon, Email, Website, Kepala Sekolah, NIP Kepala Sekolah, Tahun Berdiri, Akreditasi, Jumlah Guru, Jumlah TU, Jumlah Siswa, Latitude, Longitude'),
            ]);
    }

    public function import(): void
    {
        $data = $this->form->getState();

        $file = $data['file'];

        try {
            $filePath = null;

            if ($file instanceof TemporaryUploadedFile) {
                $filePath = $file->getRealPath();
            } elseif (is_string($file)) {
                $filePath = Storage::disk('local')->path($file);
            }

            if (!$filePath || !file_exists($filePath)) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('File tidak ditemukan. Silakan upload ulang.')
                    ->send();
                return;
            }

            $importer = new SekolahImport();
            $this->importResults = $importer->import($filePath);

            if ($this->importResults['success'] > 0) {
                Notification::make()
                    ->success()
                    ->title('Import Berhasil')
                    ->body("Berhasil: {$this->importResults['success']} sekolah. Gagal: {$this->importResults['failed']}. Dilewati: {$this->importResults['skipped']}.")
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Import Selesai')
                    ->body("Tidak ada data yang berhasil diimport. Gagal: {$this->importResults['failed']}. Dilewati: {$this->importResults['skipped']}.")
                    ->persistent()
                    ->send();
            }
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->danger()
                ->title('Format File Salah')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Import')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () {
            $options = new \OpenSpout\Writer\XLSX\Options();
            $writer = new \OpenSpout\Writer\XLSX\Writer($options);
            $writer->openToFile('php://output');

            // Header row
            $headerRow = \OpenSpout\Common\Entity\Row::fromValues([
                'NPSN',
                'Nama',
                'Jenjang',
                'Status',
                'Alamat',
                'Kelurahan',
                'Kecamatan',
                'Kabupaten',
                'Provinsi',
                'Kode Pos',
                'Telepon',
                'Email',
                'Website',
                'Kepala Sekolah',
                'NIP Kepala Sekolah',
                'Tahun Berdiri',
                'Akreditasi',
                'Jumlah Guru',
                'Jumlah TU',
                'Jumlah Siswa',
                'Latitude',
                'Longitude',
            ]);

            $writer->addRow($headerRow);

            // Example row
            $exampleRow = \OpenSpout\Common\Entity\Row::fromValues([
                '10100001',
                'SDN 1 Contoh',
                'SD',
                'Negeri',
                'Jl. Contoh No. 1',
                'Kelurahan Contoh',
                'Kecamatan Contoh',
                'Kabupaten Contoh',
                'Provinsi Contoh',
                '12345',
                '021-1234567',
                'sdn1contoh@gmail.com',
                'www.sdn1contoh.sch.id',
                'Nama Kepala Sekolah',
                '196501011990031001',
                '1990',
                'A',
                '30',
                '10',
                '500',
                '-6.200000',
                '106.800000',
            ]);

            $writer->addRow($exampleRow);
            $writer->close();
        }, 'template_import_sekolah.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadAccountsReport(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $accounts = $this->importResults['created_accounts'] ?? [];

        return response()->streamDownload(function () use ($accounts) {
            $options = new \OpenSpout\Writer\XLSX\Options();
            $writer = new \OpenSpout\Writer\XLSX\Writer($options);
            $writer->openToFile('php://output');

            $headerRow = \OpenSpout\Common\Entity\Row::fromValues([
                'NPSN',
                'Nama Sekolah',
                'Email (Username)',
                'Password',
            ]);
            $writer->addRow($headerRow);

            foreach ($accounts as $account) {
                $row = \OpenSpout\Common\Entity\Row::fromValues([
                    $account['npsn'],
                    $account['nama'],
                    $account['email'],
                    $account['password'],
                ]);
                $writer->addRow($row);
            }

            $writer->close();
        }, 'akun_sekolah_' . date('Y-m-d_His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(SekolahResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action('downloadTemplate'),
        ];
    }
}
