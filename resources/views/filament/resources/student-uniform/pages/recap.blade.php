<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filter --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @if (!$this->isSekolahRole)
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sekolah</label>
                        <select wire:model.live="selectedSekolahId"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm">
                            <option value="">-- Semua Sekolah --</option>
                            @foreach ($this->sekolahOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ajaran</label>
                    <select wire:model.live="selectedPeriodId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($this->periodOptions as $id => $year)
                            <option value="{{ $id }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Siswa: <span class="font-bold text-primary-600">{{ $this->totalSiswa }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Nama Sekolah Header --}}
        @if ($this->selectedSekolahId)
            <div class="rounded-xl bg-primary-50 p-4 dark:bg-primary-900/20">
                <p class="text-sm text-gray-600 dark:text-gray-400">Nama Sekolah</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $this->selectedSekolahName }}</p>
            </div>
        @endif

        {{-- PAKAIAN SISWA --}}
        <div
            class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    PAKAIAN SISWA
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Seragam Putih Merah, Seragam Pramuka, Seragam
                    Olah Raga, Seragam Batik</p>
            </div>

            @foreach ($this->pakaianRecap as $category)
                <div class="p-4 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ $category['label'] }}
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 w-12">No
                                    </th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-400 w-20">
                                        Kelas</th>
                                    @foreach ($category['sizes'] as $size)
                                        <th
                                            class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-400 min-w-[50px]">
                                            {{ $size }}</th>
                                    @endforeach
                                    <th
                                        class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($category['matrix'] as $kelas => $row)
                                    <tr
                                        class="{{ $kelas === 'Jumlah' ? 'bg-primary-50 dark:bg-primary-900/20 font-bold' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }} border-t border-gray-100 dark:border-gray-800">
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">
                                            {{ $kelas !== 'Jumlah' ? $no++ : '' }}</td>
                                        <td
                                            class="px-3 py-2 {{ $kelas === 'Jumlah' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $kelas }}</td>
                                        @foreach ($category['sizes'] as $size)
                                            <td
                                                class="px-3 py-2 text-center {{ $row[$size] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                                                {{ $row[$size] > 0 ? $row[$size] : '-' }}
                                            </td>
                                        @endforeach
                                        <td
                                            class="px-3 py-2 text-center bg-gray-50 dark:bg-gray-800 font-semibold text-gray-900 dark:text-white">
                                            {{ $row['total_row'] > 0 ? $row['total_row'] : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SEPATU --}}
        <div
            class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">SEPATU</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th class="px-2 py-2 text-left font-medium text-gray-600 dark:text-gray-400 w-12"
                                    rowspan="2">No</th>
                                <th class="px-2 py-2 text-left font-medium text-gray-600 dark:text-gray-400 w-20"
                                    rowspan="2">Kelas</th>
                                <th class="px-2 py-2 text-center font-medium text-gray-600 dark:text-gray-400"
                                    colspan="{{ count($this->sepatuRecap['sizes']) }}">SIZE / UKURAN</th>
                                <th class="px-2 py-2 text-center font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700"
                                    rowspan="2">Total</th>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                @foreach ($this->sepatuRecap['sizes'] as $size)
                                    <th
                                        class="px-2 py-2 text-center font-medium text-gray-600 dark:text-gray-400 min-w-[40px]">
                                        {{ $size }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($this->sepatuRecap['matrix'] as $kelas => $row)
                                <tr
                                    class="{{ $kelas === 'Jumlah' ? 'bg-primary-50 dark:bg-primary-900/20 font-bold' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }} border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-2 py-2 text-gray-600 dark:text-gray-400">
                                        {{ $kelas !== 'Jumlah' ? $no++ : '' }}</td>
                                    <td
                                        class="px-2 py-2 {{ $kelas === 'Jumlah' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $kelas }}</td>
                                    @foreach ($this->sepatuRecap['sizes'] as $size)
                                        <td
                                            class="px-2 py-2 text-center {{ $row[$size] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                                            {{ $row[$size] > 0 ? $row[$size] : '-' }}
                                        </td>
                                    @endforeach
                                    <td
                                        class="px-2 py-2 text-center bg-gray-50 dark:bg-gray-800 font-semibold text-gray-900 dark:text-white">
                                        {{ $row['total_row'] > 0 ? $row['total_row'] : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div>
            <a href="{{ \App\Filament\Resources\StudentUniformResource::getUrl('index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Kembali ke daftar siswa
            </a>
        </div>
    </div>
</x-filament-panels::page>
