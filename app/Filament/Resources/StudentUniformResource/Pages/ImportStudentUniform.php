<?php

namespace App\Filament\Resources\StudentUniformResource\Pages;

use App\Filament\Resources\StudentUniformResource;
use App\Imports\StudentUniformImport;
use App\Models\AcademicPeriod;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportStudentUniform extends Page
{
    protected static string $resource = StudentUniformResource::class;

    protected string $view = 'filament.resources.student-uniform.pages.import';

    protected static ?string $title = 'Import Data Seragam Siswa';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

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
                    ->maxSize(2048)
                    ->required()
                    ->storeFiles(false)
                    ->helperText('Upload file Excel (.xlsx) dengan kolom: Nama Siswa, NISN (opsional), Jenis Kelamin (L/P), Kelas (I-VI), Ukuran Baju, Ukuran Celana/Rok (S-XXXL), Ukuran Sepatu (28-44)'),
            ]);
    }

    public function import(): void
    {
        $data = $this->form->getState();
        $file = $data['file'];

        $user = Auth::user();
        $sekolahId = $user->sekolahs()->first()?->id;

        if (!$sekolahId) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Akun Anda tidak terhubung dengan sekolah manapun.')
                ->send();
            return;
        }

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

            $importer = new StudentUniformImport($sekolahId);
            $this->importResults = $importer->import($filePath);

            $activePeriod = AcademicPeriod::where('is_active', true)->first();
            $this->importResults['tahun_ajaran'] = $activePeriod?->year ?? '-';

            if ($this->importResults['success'] > 0) {
                Notification::make()
                    ->success()
                    ->title('Import Berhasil')
                    ->body("Berhasil: {$this->importResults['success']} siswa. Gagal: {$this->importResults['failed']}. Dilewati: {$this->importResults['skipped']}.")
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

            $headerRow = \OpenSpout\Common\Entity\Row::fromValues([
                'Nama Siswa',
                'NISN',
                'Jenis Kelamin',
                'Kelas',
                'Ukuran Baju',
                'Ukuran Celana/Rok',
                'Ukuran Sepatu',
            ]);
            $writer->addRow($headerRow);

            $example1 = \OpenSpout\Common\Entity\Row::fromValues([
                'Ahmad Fauzi',
                '0012345678',
                'L',
                'I',
                'M',
                'M',
                '30',
            ]);
            $writer->addRow($example1);

            $example2 = \OpenSpout\Common\Entity\Row::fromValues([
                'Siti Nurhaliza',
                '0012345679',
                'P',
                'III',
                'S',
                'S',
                '28',
            ]);
            $writer->addRow($example2);

            $writer->close();
        }, 'template_import_seragam_siswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(StudentUniformResource::getUrl('index'))
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
