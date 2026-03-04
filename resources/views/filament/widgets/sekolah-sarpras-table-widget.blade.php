<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Data Sarana Prasarana
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="border border-primary-700 px-3 py-2 text-center" rowspan="2" style="width: 40px;">#
                        </th>
                        <th class="border border-primary-700 px-3 py-2 text-left" rowspan="2">Sarana Prasarana</th>
                        <th class="border border-primary-700 px-3 py-2 text-center" rowspan="2" style="width: 90px;">
                            Jumlah Tersedia</th>
                        <th class="border border-primary-700 px-3 py-2 text-center" colspan="2">Kondisi</th>
                        <th class="border border-primary-700 px-3 py-2 text-center" rowspan="2">Keterangan</th>
                        <th class="border border-primary-700 px-3 py-2 text-center" rowspan="2" style="width: 80px;">
                            Aksi</th>
                    </tr>
                    <tr class="bg-primary-600 text-white">
                        <th class="border border-primary-700 px-3 py-2 text-center" style="width: 60px;">Baik</th>
                        <th class="border border-primary-700 px-3 py-2 text-center" style="width: 60px;">Rusak</th>
                    </tr>
                    @if ($semesterLabel)
                        <tr class="bg-primary-500 text-white">
                            <th class="border border-primary-600 px-3 py-1 text-center" colspan="7">
                                {{ $semesterLabel }}</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @php $globalIndex = 0; @endphp
                    @forelse ($categories as $category)
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-gray-700 dark:text-gray-200"
                                colspan="7">
                                {{ $category->name }}
                            </td>
                        </tr>
                        @forelse ($category->sarprasTypes as $type)
                            @php
                                $globalIndex++;
                                $data = $sarprasData[$type->id] ?? null;
                                $jumlah = $data?->jumlah ?? '-';
                                $baik = $data?->kondisi_baik ?? '-';
                                $rusak = $data
                                    ? $data->kondisi_rusak_ringan +
                                        $data->kondisi_rusak_sedang +
                                        $data->kondisi_rusak_berat
                                    : '-';
                                $keterangan = $data?->keterangan ?? '-';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-600 dark:text-gray-400">
                                    {{ $globalIndex }}</td>
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-gray-700 dark:text-gray-300">
                                    {{ $type->name }}
                                </td>
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-700 dark:text-gray-300">
                                    {{ $jumlah }}</td>
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-700 dark:text-gray-300">
                                    {{ $baik }}</td>
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-700 dark:text-gray-300">
                                    {{ $rusak }}</td>
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-gray-700 dark:text-gray-300">
                                    {{ $keterangan }}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">
                                    @if ($data)
                                        <a href="{{ \App\Filament\Resources\SekolahSarprasResource::getUrl('edit', ['record' => $data->id]) }}"
                                            class="text-primary-600 hover:text-primary-500 text-xs underline">
                                            Edit
                                        </a>
                                    @else
                                        <a href="{{ \App\Filament\Resources\SekolahSarprasResource::getUrl('create') }}"
                                            class="text-success-600 hover:text-success-500 text-xs underline">
                                            + Tambah
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-400"
                                    colspan="7">
                                    Belum ada tipe sarpras
                                </td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td class="border border-gray-300 dark:border-gray-600 px-3 py-4 text-center text-gray-400"
                                colspan="7">
                                Belum ada data kategori sarana prasarana
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
