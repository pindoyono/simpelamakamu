<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveResource\Pages;
use App\Models\Archive;
use App\Models\Sekolah;
use App\Models\AcademicPeriod;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Storage;

class ArchiveResource extends Resource
{
    protected static ?string $model = Archive::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Arsip Dokumen';

    protected static ?string $modelLabel = 'Arsip Dokumen';

    protected static ?string $pluralModelLabel = 'Arsip Dokumen';

    public static function getNavigationGroup(): ?string
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            return null;
        }
        return 'Manajemen SARPRAS';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Document Archive')
                    ->tabs([
                        Tabs\Tab::make('Informasi Dokumen')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('sekolah_id')
                                            ->label('Sekolah')
                                            ->relationship('sekolah', 'nama')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false),

                                        Select::make('academic_period_id')
                                            ->label('Tahun Ajaran')
                                            ->relationship('academicPeriod', 'year')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->default(fn () => AcademicPeriod::where('is_active', true)->first()?->id),

                                        TextInput::make('title')
                                            ->label('Judul Dokumen')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Select::make('document_type')
                                            ->label('Jenis Dokumen')
                                            ->required()
                                            ->options([
                                                'sarpras_report' => 'Laporan SARPRAS',
                                                'procurement_proposal' => 'Usulan Pengadaan',
                                                'maintenance_record' => 'Catatan Pemeliharaan',
                                                'inspection_report' => 'Laporan Inspeksi',
                                                'inventory_list' => 'Daftar Inventaris',
                                                'budget_document' => 'Dokumen Anggaran',
                                                'contract' => 'Kontrak',
                                                'photo_documentation' => 'Dokumentasi Foto',
                                                'certificate' => 'Sertifikat',
                                                'other' => 'Lainnya',
                                            ])
                                            ->native(false),

                                        DatePicker::make('document_date')
                                            ->label('Tanggal Dokumen')
                                            ->required()
                                            ->default(now()),
                                    ]),

                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('File & Tags')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Upload File')
                                    ->required()
                                    ->disk('public')
                                    ->directory('archives')
                                    ->maxSize(10240)
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'image/jpeg',
                                        'image/png',
                                    ])
                                    ->helperText('PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 10MB)'),

                                TextInput::make('tags')
                                    ->label('Tags')
                                    ->placeholder('Pisahkan dengan koma')
                                    ->helperText('Contoh: laporan, semester 1, 2024'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sekolah.nama')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('academicPeriod.year')
                    ->label('Tahun')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('document_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sarpras_report' => 'success',
                        'inventory' => 'info',
                        'procurement' => 'warning',
                        'maintenance' => 'primary',
                        'photo' => 'gray',
                        'official_letter' => 'danger',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sarpras_report' => 'Lap. SARPRAS',
                        'inventory' => 'Inventarisasi',
                        'procurement' => 'Pengadaan',
                        'maintenance' => 'Pemeliharaan',
                        'photo' => 'Foto',
                        'official_letter' => 'Surat Resmi',
                        'other' => 'Lainnya',
                        default => $state,
                    }),

                TextColumn::make('document_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(function ($record) {
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            $size = Storage::disk('public')->size($record->file_path);
                            if ($size >= 1048576) {
                                return round($size / 1048576, 2) . ' MB';
                            }
                            return round($size / 1024, 2) . ' KB';
                        }
                        return '-';
                    })
                    ->toggleable(),

                TextColumn::make('uploader.name')
                    ->label('Uploader')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Upload')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->relationship('sekolah', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('academic_period_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicPeriod', 'year'),
                SelectFilter::make('document_type')
                    ->label('Jenis Dokumen')
                    ->options([
                        'sarpras_report' => 'Laporan SARPRAS',
                        'inventory' => 'Inventarisasi',
                        'procurement' => 'Pengadaan',
                        'maintenance' => 'Pemeliharaan',
                        'photo' => 'Dokumentasi Foto',
                        'official_letter' => 'Surat Resmi',
                        'other' => 'Lainnya',
                    ]),
            ])
            ->actions([
                Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Archive $record): string => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn (Archive $record): bool => $record->file_path && Storage::disk('public')->exists($record->file_path)),
                Action::make('preview')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Archive $record): string => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn (Archive $record): bool => $record->file_path && Storage::disk('public')->exists($record->file_path)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchives::route('/'),
            'create' => Pages\CreateArchive::route('/create'),
            'edit' => Pages\EditArchive::route('/{record}/edit'),
        ];
    }
}
