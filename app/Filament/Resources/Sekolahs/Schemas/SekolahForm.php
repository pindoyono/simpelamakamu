<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // KOLOM KIRI
                Section::make('Identitas & Kontak')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->placeholder('12345678')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(8),
                        TextInput::make('nama')
                            ->label('Nama Sekolah')
                            ->placeholder('SDN 1 Jakarta')
                            ->required()
                            ->maxLength(255),
                        Select::make('jenjang')
                            ->label('Jenjang')
                            ->options([
                                'SD' => 'SD',
                                'SMP' => 'SMP',
                                'SMA' => 'SMA',
                                'SMK' => 'SMK',
                            ])
                            ->required()
                            ->default('SD'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Negeri' => 'Negeri',
                                'Swasta' => 'Swasta',
                            ])
                            ->required()
                            ->default('Negeri'),
                        TextInput::make('tahun_berdiri')
                            ->label('Tahun Berdiri')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y'))
                            ->placeholder('1980'),
                        Select::make('akreditasi')
                            ->label('Akreditasi')
                            ->options([
                                'A' => 'A (Unggul)',
                                'B' => 'B (Baik)',
                                'C' => 'C (Cukup)',
                                'Belum' => 'Belum',
                            ]),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('021-1234567'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('sekolah@email.com'),
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://sekolah.sch.id'),
                        TextInput::make('kepala_sekolah')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),
                        TextInput::make('nip_kepala_sekolah')
                            ->label('NIP Kepala Sekolah')
                            ->maxLength(30),
                        FileUpload::make('logo')
                            ->label('Logo Sekolah')
                            ->image()
                            ->directory('logos')
                            ->maxSize(1024),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),

                // KOLOM KANAN
                Section::make('Alamat & Lokasi')
                    ->columnSpan(1)
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->placeholder('Jl. Pendidikan No. 1')
                            ->rows(2),
                        TextInput::make('kelurahan')
                            ->label('Kelurahan')
                            ->maxLength(100),
                        TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->maxLength(100),
                        TextInput::make('kabupaten')
                            ->label('Kab/Kota')
                            ->maxLength(100),
                        TextInput::make('provinsi')
                            ->label('Provinsi')
                            ->maxLength(100),
                        TextInput::make('kode_pos')
                            ->label('Kode Pos')
                            ->maxLength(10)
                            ->placeholder('12345'),
                        Map::make('location')
                            ->label('Peta Lokasi')
                            ->defaultLocation(latitude: 3.5700, longitude: 116.6300)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, callable $set) {
                                if ($record && $record->latitude && $record->longitude) {
                                    $set('location', [
                                        'lat' => (float) $record->latitude,
                                        'lng' => (float) $record->longitude,
                                    ]);
                                }
                            })
                            ->showMarker()
                            ->markerColor('#3b82f6')
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->draggable()
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->zoom(15)
                            ->detectRetina()
                            ->extraStyles([
                                'min-height: 300px',
                                'border-radius: 8px',
                            ]),
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->placeholder('3.5700'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->placeholder('116.6300'),
                    ]),
            ]);
    }
}
