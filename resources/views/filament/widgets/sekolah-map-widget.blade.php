<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Profil Sekolah
        </x-slot>

        @if ($primarySekolah)
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;" class="mb-6 text-sm">
                {{-- Kolom Kiri --}}
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top"
                                style="width: 140px;">Sekolah</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                NPSN</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->npsn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Alamat</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Telp</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Akreditasi</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->akreditasi ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Kolom Kanan --}}
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top"
                                style="width: 210px;">Kepala Sekolah</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->kepala_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Jml. Tenaga Pendidik (Guru)</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->jumlah_guru ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Jml. Tenaga Kependidikan (TU)</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->jumlah_tu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Jumlah Siswa</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                {{ $primarySekolah->jumlah_siswa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap align-top">
                                Status Tanah</td>
                            <td class="py-1 text-gray-700 dark:text-gray-300 align-top">:
                                @if ($primarySekolah->sertifikat_tanah)
                                    <a href="{{ Storage::disk('public')->url($primarySekolah->sertifikat_tanah) }}"
                                        target="_blank" class="text-primary-600 hover:text-primary-500 underline">
                                        Dokumen ↗
                                    </a>
                                @else
                                    {{ $primarySekolah->status_tanah ?? '-' }}
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        @endif

        {{ $this->form }}
    </x-filament::section>
</x-filament-widgets::widget>
