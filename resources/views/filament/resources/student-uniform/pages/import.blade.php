<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Card --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" style="width: 24px; height: 24px;" class="text-primary-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Petunjuk Import Seragam Siswa</h3>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 space-y-1">
                        <p>1. Download template Excel terlebih dahulu menggunakan tombol <strong>"Download
                                Template"</strong> di atas.</p>
                        <p>2. Isi data siswa pada file template. Kolom wajib: <strong>Nama Siswa, Jenis Kelamin (L/P),
                                Kelas (I-VI), Ukuran Baju, Ukuran Celana/Rok (S-XXXL), Ukuran Sepatu (28-44)</strong>.
                        </p>
                        <p>3. Kolom opsional: <strong>NISN</strong>.</p>
                        <p>4. Upload file Excel yang sudah diisi, kemudian klik tombol <strong>"Import Data"</strong>.
                        </p>
                        <p>5. <strong>Tahun ajaran otomatis</strong> menggunakan tahun ajaran yang sedang aktif.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <form wire:submit="import">
                {{ $this->form }}

                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="import">Import Data</span>
                        <span wire:loading wire:target="import">Memproses...</span>
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if ($importResults)
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Hasil Import</h3>

                {{-- Tahun Ajaran Info --}}
                <div class="mb-4 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/20">
                    <p class="text-sm text-primary-700 dark:text-primary-300">
                        Tahun Ajaran: <strong>{{ $importResults['tahun_ajaran'] ?? '-' }}</strong>
                    </p>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="rounded-lg bg-success-50 dark:bg-success-400/10 p-4">
                        <div class="text-sm font-medium text-success-600 dark:text-success-400">Berhasil</div>
                        <div class="text-2xl font-bold text-success-700 dark:text-success-300">
                            {{ $importResults['success'] }}</div>
                    </div>
                    <div class="rounded-lg bg-danger-50 dark:bg-danger-400/10 p-4">
                        <div class="text-sm font-medium text-danger-600 dark:text-danger-400">Gagal</div>
                        <div class="text-2xl font-bold text-danger-700 dark:text-danger-300">
                            {{ $importResults['failed'] }}</div>
                    </div>
                    <div class="rounded-lg bg-warning-50 dark:bg-warning-400/10 p-4">
                        <div class="text-sm font-medium text-warning-600 dark:text-warning-400">Dilewati</div>
                        <div class="text-2xl font-bold text-warning-700 dark:text-warning-300">
                            {{ $importResults['skipped'] }}</div>
                    </div>
                </div>

                {{-- Errors --}}
                @if (count($importResults['errors']) > 0)
                    <div>
                        <h4 class="text-sm font-semibold text-danger-600 dark:text-danger-400 mb-2">
                            Error & Peringatan ({{ count($importResults['errors']) }})
                        </h4>
                        <div class="max-h-60 overflow-y-auto rounded-lg bg-danger-50 dark:bg-danger-400/10 p-4">
                            <ul class="space-y-1 text-sm text-danger-700 dark:text-danger-300">
                                @foreach ($importResults['errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
