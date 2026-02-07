<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcurementProposalResource\Pages;
use App\Models\ProcurementProposal;
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
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ProcurementProposalResource extends Resource
{
    protected static ?string $model = ProcurementProposal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen SARPRAS';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Usulan Pengadaan';

    protected static ?string $modelLabel = 'Usulan Pengadaan';

    protected static ?string $pluralModelLabel = 'Usulan Pengadaan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Procurement Proposal')
                    ->tabs([
                        Tabs\Tab::make('Informasi Utama')
                            ->icon('heroicon-o-information-circle')
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
                                            ->label('Judul Usulan')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Select::make('priority')
                                            ->label('Prioritas')
                                            ->required()
                                            ->options([
                                                'low' => 'Rendah',
                                                'medium' => 'Sedang',
                                                'high' => 'Tinggi',
                                                'urgent' => 'Mendesak',
                                            ])
                                            ->default('medium')
                                            ->native(false),

                                        Select::make('category')
                                            ->label('Kategori')
                                            ->required()
                                            ->options([
                                                'new_procurement' => 'Pengadaan Baru',
                                                'repair' => 'Perbaikan',
                                                'replacement' => 'Penggantian',
                                                'upgrade' => 'Peningkatan',
                                            ])
                                            ->native(false),
                                    ]),
                            ]),
                        Tabs\Tab::make('Detail Usulan')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->rows(4),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(1),

                                        TextInput::make('unit')
                                            ->label('Satuan')
                                            ->required()
                                            ->default('unit'),

                                        TextInput::make('estimated_budget')
                                            ->label('Estimasi Anggaran')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required(),
                                    ]),

                                Textarea::make('justification')
                                    ->label('Justifikasi')
                                    ->helperText('Alasan dan dasar usulan pengadaan')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('Status')
                            ->icon('heroicon-o-flag')
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'submitted' => 'Diajukan',
                                        'under_review' => 'Sedang Ditinjau',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn (): bool => !Auth::user()?->hasRole('super_admin')),

                                Textarea::make('review_notes')
                                    ->label('Catatan Review')
                                    ->rows(3),
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
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new_procurement' => 'success',
                        'repair' => 'warning',
                        'replacement' => 'info',
                        'upgrade' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new_procurement' => 'Pengadaan Baru',
                        'repair' => 'Perbaikan',
                        'replacement' => 'Penggantian',
                        'upgrade' => 'Peningkatan',
                        default => $state,
                    }),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                        'urgent' => 'Mendesak',
                        default => $state,
                    }),

                TextColumn::make('estimated_budget')
                    ->label('Anggaran')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'info',
                        'under_review' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Ditinjau',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->relationship('sekolah', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Ditinjau',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                        'urgent' => 'Mendesak',
                    ]),
                SelectFilter::make('category')
                    ->options([
                        'new_procurement' => 'Pengadaan Baru',
                        'repair' => 'Perbaikan',
                        'replacement' => 'Penggantian',
                        'upgrade' => 'Peningkatan',
                    ]),
            ])
            ->actions([
                Action::make('submit')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update(['status' => 'submitted']))
                    ->visible(fn (ProcurementProposal $record): bool => $record->status === 'draft'),
                Action::make('review')
                    ->label('Tinjau')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update(['status' => 'under_review']))
                    ->visible(fn (ProcurementProposal $record): bool => $record->status === 'submitted'),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (ProcurementProposal $record): bool => $record->status === 'under_review'),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (ProcurementProposal $record): bool => in_array($record->status, ['submitted', 'under_review'])),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProcurementProposals::route('/'),
            'create' => Pages\CreateProcurementProposal::route('/create'),
            'edit' => Pages\EditProcurementProposal::route('/{record}/edit'),
        ];
    }
}
