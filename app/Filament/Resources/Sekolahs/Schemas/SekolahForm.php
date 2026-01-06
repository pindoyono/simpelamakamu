<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
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
            ->components([
                Section::make('Identitas Sekolah')
                    ->description('Informasi dasar identitas sekolah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->placeholder('Contoh: 12345678')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(8),
                        TextInput::make('nama')
                            ->label('Nama Sekolah')
                            ->placeholder('Contoh: SDN 1 Jakarta')
                            ->required()
                            ->maxLength(255),
                        Select::make('jenjang')
                            ->label('Jenjang Pendidikan')
                            ->options([
                                'SD' => 'SD (Sekolah Dasar)',
                                'SMP' => 'SMP (Sekolah Menengah Pertama)',
                                'SMA' => 'SMA (Sekolah Menengah Atas)',
                                'SMK' => 'SMK (Sekolah Menengah Kejuruan)',
                            ])
                            ->required()
                            ->default('SD'),
                        Select::make('status')
                            ->label('Status Sekolah')
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
                            ->placeholder('Contoh: 1980'),
                        Select::make('akreditasi')
                            ->label('Akreditasi')
                            ->options([
                                'A' => 'A (Unggul)',
                                'B' => 'B (Baik)',
                                'C' => 'C (Cukup)',
                                'Belum' => 'Belum Terakreditasi',
                            ]),
                    ]),

                Section::make('Alamat Sekolah')
                    ->description('Informasi lokasi dan alamat sekolah')
                    ->columns(2)
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->placeholder('Jl. Pendidikan No. 1')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('kelurahan')
                            ->label('Kelurahan/Desa')
                            ->maxLength(100),
                        TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->maxLength(100),
                        TextInput::make('kabupaten')
                            ->label('Kabupaten/Kota')
                            ->maxLength(100),
                        TextInput::make('provinsi')
                            ->label('Provinsi')
                            ->maxLength(100),
                        TextInput::make('kode_pos')
                            ->label('Kode Pos')
                            ->maxLength(10)
                            ->placeholder('12345'),
                    ]),

                Section::make('Kontak')
                    ->description('Informasi kontak sekolah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('telepon')
                            ->label('Nomor Telepon')
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
                            ->placeholder('https://sekolah.sch.id')
                            ->columnSpanFull(),
                    ]),

                Section::make('Kepala Sekolah')
                    ->description('Informasi kepala sekolah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kepala_sekolah')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),
                        TextInput::make('nip_kepala_sekolah')
                            ->label('NIP Kepala Sekolah')
                            ->maxLength(30),
                    ]),

                Section::make('Lainnya')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Sekolah')
                            ->image()
                            ->directory('logos')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Sekolah yang tidak aktif tidak akan muncul di daftar')
                            ->default(true),
                    ]),
            ]);
    }
}
