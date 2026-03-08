<x-filament-panels::page>
    <style>
        .recap-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
        }

        .recap-table th,
        .recap-table td {
            padding: 0.5rem 0.625rem;
            text-align: center;
            white-space: nowrap;
            border: 1px solid rgb(229 231 235);
        }

        .dark .recap-table th,
        .dark .recap-table td {
            border-color: rgb(55 65 81);
        }

        .recap-table th {
            font-weight: 600;
        }

        .recap-table thead th {
            background: rgb(249 250 251);
            color: rgb(107 114 128);
        }

        .dark .recap-table thead th {
            background: rgb(31 41 55);
            color: rgb(156 163 175);
        }

        .recap-table tbody tr:hover {
            background: rgb(249 250 251);
        }

        .dark .recap-table tbody tr:hover {
            background: rgb(31 41 55 / 0.5);
        }

        .recap-table td.cell-zero {
            color: rgb(209 213 219);
        }

        .dark .recap-table td.cell-zero {
            color: rgb(75 85 99);
        }

        .recap-table td.cell-value {
            color: rgb(17 24 39);
            font-weight: 500;
        }

        .dark .recap-table td.cell-value {
            color: rgb(243 244 246);
        }

        .recap-table .row-total {
            background: rgb(239 246 255);
            font-weight: 700;
        }

        .dark .recap-table .row-total {
            background: rgb(30 58 138 / 0.15);
        }

        .recap-table .row-total td {
            color: rgb(37 99 235);
        }

        .dark .recap-table .row-total td {
            color: rgb(96 165 250);
        }

        .recap-table .col-sticky-left {
            position: sticky;
            left: 0;
            z-index: 1;
            background: inherit;
        }

        .recap-table .col-sticky-left-2 {
            position: sticky;
            left: 2.5rem;
            z-index: 1;
            background: inherit;
        }

        .recap-table thead .col-sticky-left,
        .recap-table thead .col-sticky-left-2 {
            z-index: 2;
        }

        .recap-table .col-total {
            background: rgb(249 250 251);
            font-weight: 600;
        }

        .dark .recap-table .col-total {
            background: rgb(31 41 55);
        }

        .recap-table .row-total .col-total {
            background: rgb(219 234 254);
        }

        .dark .recap-table .row-total .col-total {
            background: rgb(30 58 138 / 0.3);
        }

        @media (max-width: 640px) {

            .recap-table th,
            .recap-table td {
                padding: 0.375rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="space-y-6">

        {{-- Filter Card --}}
        <x-filament::section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                @if (!$this->isSekolahRole)
                    <div>
                        <label for="sekolah-filter"
                            class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">Sekolah</label>
                        <select id="sekolah-filter" wire:model.live="selectedSekolahId"
                            class="fi-select-input mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-950 shadow-sm transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:bg-white/5 dark:border-gray-600 dark:text-white text-sm">
                            <option value="">-- Semua Sekolah --</option>
                            @foreach ($this->sekolahOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label for="period-filter"
                        class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">Tahun
                        Ajaran</label>
                    <select id="period-filter" wire:model.live="selectedPeriodId"
                        class="fi-select-input mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-950 shadow-sm transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:bg-white/5 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($this->periodOptions as $id => $year)
                            <option value="{{ $id }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-primary-50 dark:bg-primary-400/10 px-4 py-2.5 w-full text-center">
                        <span class="text-xs font-medium text-primary-600 dark:text-primary-400 block">Total
                            Siswa</span>
                        <span
                            class="text-xl font-bold text-primary-700 dark:text-primary-300">{{ $this->totalSiswa }}</span>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Nama Sekolah --}}
        @if ($this->selectedSekolahId)
            <div class="fi-section rounded-xl bg-primary-50 dark:bg-primary-400/10 px-4 py-3 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" class="text-primary-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>
                <div>
                    <p class="text-xs text-primary-600/70 dark:text-primary-400/70">Nama Sekolah</p>
                    <p class="text-sm font-semibold text-primary-700 dark:text-primary-300">
                        {{ $this->selectedSekolahName }}</p>
                </div>
            </div>
        @endif

        {{-- PAKAIAN SISWA --}}
        <x-filament::section heading="Pakaian Siswa" description="Seragam Putih Merah, Seragam Pramuka, Seragam Olah Raga, Seragam Batik" icon="heroicon-o-squares-2x2">
            <div class="recap-table-wrapper -mx-4 sm:mx-0">
                <table class="recap-table">
                    <thead>
                        <tr>
                            <th class="col-sticky-left text-left" style="width:2.5rem" rowspan="2">No</th>
                            <th class="col-sticky-left-2 text-left" style="min-width:4rem" rowspan="2">Kelas</th>
                            @foreach ($this->pakaianRecap as $category)
                                <th colspan="{{ count($category['sizes']) }}">{{ $category['label'] }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($this->pakaianRecap as $category)
                                @foreach ($category['sizes'] as $size)
                                    <th style="min-width:2.5rem">{{ $size }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $kelasList = $this->kelasList;
                        @endphp
                        @foreach ($kelasList as $kelas)
                            <tr>
                                <td class="col-sticky-left text-left">{{ $no++ }}</td>
                                <td class="col-sticky-left-2 text-left font-medium">{{ $kelas }}</td>
                                @foreach ($this->pakaianRecap as $category)
                                    @foreach ($category['sizes'] as $size)
                                        <td class="{{ ($category['matrix'][$kelas][$size] ?? 0) > 0 ? 'cell-value' : 'cell-zero' }}">
                                            {{ ($category['matrix'][$kelas][$size] ?? 0) > 0 ? $category['matrix'][$kelas][$size] : '-' }}
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                        <tr class="row-total">
                            <td class="col-sticky-left text-left"></td>
                            <td class="col-sticky-left-2 text-left font-medium">Jumlah</td>
                            @foreach ($this->pakaianRecap as $category)
                                @foreach ($category['sizes'] as $size)
                                    <td>{{ ($category['matrix']['Jumlah'][$size] ?? 0) > 0 ? $category['matrix']['Jumlah'][$size] : '-' }}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- SEPATU --}}
        <x-filament::section heading="Sepatu" icon="heroicon-o-squares-2x2">
            <div class="recap-table-wrapper -mx-4 sm:mx-0">
                <table class="recap-table">
                    <thead>
                        <tr>
                            <th class="col-sticky-left text-left" style="width:2.5rem" rowspan="2">No</th>
                            <th class="col-sticky-left-2 text-left" style="min-width:4rem" rowspan="2">Kelas</th>
                            <th colspan="{{ count($this->sepatuRecap['sizes']) }}">Size / Ukuran</th>
                            <th class="col-total" style="min-width:3.5rem" rowspan="2">Total</th>
                        </tr>
                        <tr>
                            @foreach ($this->sepatuRecap['sizes'] as $size)
                                <th style="min-width:2.25rem">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($this->sepatuRecap['matrix'] as $kelas => $row)
                            <tr class="{{ $kelas === 'Jumlah' ? 'row-total' : '' }}">
                                <td class="col-sticky-left text-left">{{ $kelas !== 'Jumlah' ? $no++ : '' }}</td>
                                <td class="col-sticky-left-2 text-left font-medium">{{ $kelas }}</td>
                                @foreach ($this->sepatuRecap['sizes'] as $size)
                                    <td class="{{ $row[$size] > 0 ? 'cell-value' : 'cell-zero' }}">
                                        {{ $row[$size] > 0 ? $row[$size] : '-' }}
                                    </td>
                                @endforeach
                                <td class="col-total">{{ $row['total_row'] > 0 ? $row['total_row'] : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
