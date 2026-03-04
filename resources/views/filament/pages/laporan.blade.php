<x-filament-panels::page>
    <style>
        .laporan-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 640px) {
            .laporan-cards {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }
        }

        @media (min-width: 1024px) {
            .laporan-cards {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        .laporan-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        @media (min-width: 640px) {
            .laporan-stat-value {
                font-size: 1.875rem;
            }
        }

        .laporan-stat-label {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            color: #6b7280;
        }

        .dark .laporan-stat-label {
            color: #9ca3af;
        }

        .laporan-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -1.5rem -1.5rem;
        }

        .laporan-table {
            width: 100%;
            min-width: 900px;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.8125rem;
        }

        .laporan-table thead th {
            padding: 0.5rem 0.625rem;
            text-align: center;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #6b7280;
            background: #f9fafb;
            white-space: nowrap;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .laporan-table thead th {
            color: #9ca3af;
            background: rgba(255, 255, 255, 0.03);
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .laporan-table thead th.text-start {
            text-align: left;
        }

        .laporan-table thead th.group-header {
            border-bottom: none;
        }

        .laporan-table tbody td {
            padding: 0.625rem;
            text-align: center;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .dark .laporan-table tbody td {
            color: #d1d5db;
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .laporan-table tbody tr:hover td {
            background: #f9fafb;
        }

        .dark .laporan-table tbody tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }

        .laporan-table tfoot td {
            padding: 0.625rem;
            text-align: center;
            font-weight: 600;
            color: #111827;
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .dark .laporan-table tfoot td {
            color: #f9fafb;
            background: rgba(255, 255, 255, 0.03);
            border-top-color: rgba(255, 255, 255, 0.1);
        }

        .laporan-cell-name {
            text-align: left !important;
            white-space: normal !important;
            min-width: 160px;
        }

        .laporan-cell-name .name {
            font-weight: 500;
            color: #111827;
        }

        .dark .laporan-cell-name .name {
            color: #f9fafb;
        }

        .laporan-cell-name .npsn {
            font-size: 0.6875rem;
            color: #9ca3af;
        }

        /* Mobile card view */
        .laporan-mobile-cards {
            display: none;
        }

        @media (max-width: 639px) {
            .laporan-desktop-table {
                display: none;
            }

            .laporan-mobile-cards {
                display: block;
            }

            .laporan-mobile-card {
                border-bottom: 1px solid #e5e7eb;
                padding: 1rem 0;
            }

            .dark .laporan-mobile-card {
                border-bottom-color: rgba(255, 255, 255, 0.05);
            }

            .laporan-mobile-card:last-child {
                border-bottom: none;
            }

            .laporan-mobile-card-header {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 0.75rem;
            }

            .laporan-mobile-card-header .num {
                color: #9ca3af;
                font-size: 0.75rem;
                font-weight: 600;
                min-width: 1.5rem;
            }

            .laporan-mobile-card-header .name {
                font-weight: 600;
                color: #111827;
                font-size: 0.875rem;
            }

            .dark .laporan-mobile-card-header .name {
                color: #f9fafb;
            }

            .laporan-mobile-card-header .npsn {
                color: #9ca3af;
                font-size: 0.6875rem;
            }

            .laporan-mobile-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }

            .laporan-mobile-item {
                text-align: center;
                padding: 0.375rem;
                background: #f9fafb;
                border-radius: 0.375rem;
            }

            .dark .laporan-mobile-item {
                background: rgba(255, 255, 255, 0.03);
            }

            .laporan-mobile-item .label {
                font-size: 0.625rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #9ca3af;
                margin-bottom: 0.125rem;
            }

            .laporan-mobile-item .value {
                font-size: 0.8125rem;
                font-weight: 600;
                color: #374151;
            }

            .dark .laporan-mobile-item .value {
                color: #d1d5db;
            }
        }
    </style>

    {{-- Summary Cards --}}
    <div class="laporan-cards">
        <x-filament::section>
            <div style="text-align: center;">
                <div class="laporan-stat-value" style="color: #6366f1;">{{ $totalSekolah }}</div>
                <div class="laporan-stat-label">Total Sekolah</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div style="text-align: center;">
                <div class="laporan-stat-value" style="color: #10b981;">{{ number_format($totalSiswa) }}</div>
                <div class="laporan-stat-label">Total Siswa</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div style="text-align: center;">
                <div class="laporan-stat-value" style="color: #3b82f6;">{{ number_format($totalGuru) }}</div>
                <div class="laporan-stat-label">Total Guru</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div style="text-align: center;">
                <div class="laporan-stat-value" style="color: #f59e0b;">{{ number_format($totalSarpras) }}</div>
                <div class="laporan-stat-label">Total Sarpras</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div style="text-align: center;">
                <div class="laporan-stat-value" style="color: #ef4444;">{{ number_format($totalProposal) }}</div>
                <div class="laporan-stat-label">Total Rehabilitasi</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Data Table --}}
    <x-filament::section>
        <x-slot name="heading">
            Rekap Data Per Sekolah
        </x-slot>

        {{-- Desktop / Tablet Table --}}
        <div class="laporan-desktop-table">
            <div class="laporan-table-wrap">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 40px;">No</th>
                            <th rowspan="2" class="text-start" style="min-width: 160px;">Sekolah</th>
                            <th rowspan="2">Jenjang</th>
                            <th colspan="3" class="group-header">SDM</th>
                            <th colspan="3" class="group-header">Sarana Prasarana</th>
                            <th colspan="3" class="group-header">Rehabilitasi</th>
                        </tr>
                        <tr>
                            <th>Siswa</th>
                            <th>Guru</th>
                            <th>TU</th>
                            <th>Jumlah</th>
                            <th>Baik</th>
                            <th>Rusak</th>
                            <th>Total</th>
                            <th>Disetujui</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="laporan-cell-name">
                                    <div class="name">{{ $data['sekolah']->nama }}</div>
                                    <div class="npsn">NPSN: {{ $data['sekolah']->npsn }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match ($data['sekolah']->jenjang) {
                                            'SD' => 'success',
                                            'SMP' => 'info',
                                            'SMA' => 'warning',
                                            'SMK' => 'danger',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-filament::badge :color="$badgeColor">
                                        {{ $data['sekolah']->jenjang }}
                                    </x-filament::badge>
                                </td>
                                <td>{{ number_format($data['jumlah_siswa']) }}</td>
                                <td>{{ number_format($data['jumlah_guru']) }}</td>
                                <td>{{ number_format($data['jumlah_tu']) }}</td>
                                <td>{{ number_format($data['total_jumlah_sarpras']) }}</td>
                                <td><x-filament::badge
                                        color="success">{{ number_format($data['kondisi_baik']) }}</x-filament::badge>
                                </td>
                                <td><x-filament::badge
                                        color="danger">{{ number_format($data['kondisi_rusak']) }}</x-filament::badge>
                                </td>
                                <td>{{ $data['total_proposal'] }}</td>
                                <td><x-filament::badge
                                        color="success">{{ $data['proposal_approved'] }}</x-filament::badge></td>
                                <td><x-filament::badge
                                        color="warning">{{ $data['proposal_pending'] }}</x-filament::badge></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" style="padding: 1.5rem; color: #9ca3af;">Belum ada data sekolah</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">TOTAL</td>
                            <td>{{ number_format(array_sum(array_column($rekapData, 'jumlah_siswa'))) }}</td>
                            <td>{{ number_format(array_sum(array_column($rekapData, 'jumlah_guru'))) }}</td>
                            <td>{{ number_format(array_sum(array_column($rekapData, 'jumlah_tu'))) }}</td>
                            <td>{{ number_format(array_sum(array_column($rekapData, 'total_jumlah_sarpras'))) }}</td>
                            <td><x-filament::badge
                                    color="success">{{ number_format(array_sum(array_column($rekapData, 'kondisi_baik'))) }}</x-filament::badge>
                            </td>
                            <td><x-filament::badge
                                    color="danger">{{ number_format(array_sum(array_column($rekapData, 'kondisi_rusak'))) }}</x-filament::badge>
                            </td>
                            <td>{{ array_sum(array_column($rekapData, 'total_proposal')) }}</td>
                            <td><x-filament::badge
                                    color="success">{{ array_sum(array_column($rekapData, 'proposal_approved')) }}</x-filament::badge>
                            </td>
                            <td><x-filament::badge
                                    color="warning">{{ array_sum(array_column($rekapData, 'proposal_pending')) }}</x-filament::badge>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Mobile Card View --}}
        <div class="laporan-mobile-cards">
            @forelse ($rekapData as $index => $data)
                <div class="laporan-mobile-card">
                    <div class="laporan-mobile-card-header">
                        <span class="num">{{ $index + 1 }}.</span>
                        <div>
                            <div class="name">
                                {{ $data['sekolah']->nama }}
                                @php
                                    $badgeColor = match ($data['sekolah']->jenjang) {
                                        'SD' => 'success',
                                        'SMP' => 'info',
                                        'SMA' => 'warning',
                                        'SMK' => 'danger',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-filament::badge :color="$badgeColor"
                                    style="display: inline-flex; vertical-align: middle; margin-left: 0.25rem;">
                                    {{ $data['sekolah']->jenjang }}
                                </x-filament::badge>
                            </div>
                            <div class="npsn">NPSN: {{ $data['sekolah']->npsn }}</div>
                        </div>
                    </div>
                    <div class="laporan-mobile-grid">
                        <div class="laporan-mobile-item">
                            <div class="label">Siswa</div>
                            <div class="value">{{ number_format($data['jumlah_siswa']) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Guru</div>
                            <div class="value">{{ number_format($data['jumlah_guru']) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">TU</div>
                            <div class="value">{{ number_format($data['jumlah_tu']) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Sarpras</div>
                            <div class="value">{{ number_format($data['total_jumlah_sarpras']) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Baik</div>
                            <div class="value"><x-filament::badge
                                    color="success">{{ number_format($data['kondisi_baik']) }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Rusak</div>
                            <div class="value"><x-filament::badge
                                    color="danger">{{ number_format($data['kondisi_rusak']) }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Rehab</div>
                            <div class="value">{{ $data['total_proposal'] }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Disetujui</div>
                            <div class="value"><x-filament::badge
                                    color="success">{{ $data['proposal_approved'] }}</x-filament::badge></div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Pending</div>
                            <div class="value"><x-filament::badge
                                    color="warning">{{ $data['proposal_pending'] }}</x-filament::badge></div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding: 1.5rem; text-align: center; color: #9ca3af;">Belum ada data sekolah</div>
            @endforelse

            {{-- Mobile Total --}}
            @if (count($rekapData) > 0)
                <div class="laporan-mobile-card" style="padding-top: 1rem; border-top: 2px solid #e5e7eb;">
                    <div class="laporan-mobile-card-header">
                        <div>
                            <div class="name">TOTAL KESELURUHAN</div>
                        </div>
                    </div>
                    <div class="laporan-mobile-grid">
                        <div class="laporan-mobile-item">
                            <div class="label">Siswa</div>
                            <div class="value">
                                {{ number_format(array_sum(array_column($rekapData, 'jumlah_siswa'))) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Guru</div>
                            <div class="value">
                                {{ number_format(array_sum(array_column($rekapData, 'jumlah_guru'))) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">TU</div>
                            <div class="value">{{ number_format(array_sum(array_column($rekapData, 'jumlah_tu'))) }}
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Sarpras</div>
                            <div class="value">
                                {{ number_format(array_sum(array_column($rekapData, 'total_jumlah_sarpras'))) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Baik</div>
                            <div class="value"><x-filament::badge
                                    color="success">{{ number_format(array_sum(array_column($rekapData, 'kondisi_baik'))) }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Rusak</div>
                            <div class="value"><x-filament::badge
                                    color="danger">{{ number_format(array_sum(array_column($rekapData, 'kondisi_rusak'))) }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Rehab</div>
                            <div class="value">{{ array_sum(array_column($rekapData, 'total_proposal')) }}</div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Disetujui</div>
                            <div class="value"><x-filament::badge
                                    color="success">{{ array_sum(array_column($rekapData, 'proposal_approved')) }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="laporan-mobile-item">
                            <div class="label">Pending</div>
                            <div class="value"><x-filament::badge
                                    color="warning">{{ array_sum(array_column($rekapData, 'proposal_pending')) }}</x-filament::badge>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-panels::page>
