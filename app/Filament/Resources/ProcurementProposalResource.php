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

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pengadaan/Rehabilitasi';

    protected static ?string $modelLabel = 'Pengadaan/Rehabilitasi';

    protected static ?string $pluralModelLabel = 'Pengadaan/Rehabilitasi';

    public static function getNavigationGroup(): ?string
    {
        $user = Auth::user();
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            return null;
        }
        return 'Manajemen SARPRAS';
    }

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
        $userSekolahId = $isSekolahRole ? $user->sekolahs()->first()?->id : null;

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
                                            ->native(false)
                                            ->hidden($isSekolahRole)
                                            ->default($userSekolahId),

                                        \Filament\Forms\Components\Hidden::make('sekolah_id')
                                            ->default($userSekolahId)
                                            ->visible($isSekolahRole),

                                        Select::make('academic_period_id')
                                            ->label('Tahun Ajaran')
                                            ->options(function () {
                                                return AcademicPeriod::orderBy('year', 'desc')
                                                    ->get()
                                                    ->mapWithKeys(fn ($period) => [
                                                        $period->id => $period->year . ($period->is_active ? ' ✓ (Aktif)' : '')
                                                    ]);
                                            })
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
                                                'critical' => 'Mendesak',
                                            ])
                                            ->default('medium')
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

                                TextInput::make('total_budget')
                                    ->label('Total Anggaran')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
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

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                        'critical' => 'Mendesak',
                        default => $state,
                    }),

                TextColumn::make('total_budget')
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
                        'critical' => 'Mendesak',
                    ]),
            ])
            ->actions([
                Action::make('submit')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update([
                        'status' => 'submitted',
                        'submitted_by' => Auth::id(),
                        'submitted_at' => now(),
                    ]))
                    ->visible(fn (ProcurementProposal $record): bool => $record->status === 'draft'),
                Action::make('review')
                    ->label('Tinjau')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update([
                        'status' => 'under_review',
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                    ]))
                    ->visible(function (ProcurementProposal $record): bool {
                        $user = Auth::user();
                        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
                        return !$isSekolahRole && $record->status === 'submitted';
                    }),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update([
                        'status' => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]))
                    ->visible(function (ProcurementProposal $record): bool {
                        $user = Auth::user();
                        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
                        return !$isSekolahRole && $record->status === 'under_review';
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (ProcurementProposal $record) => $record->update([
                        'status' => 'rejected',
                        'rejected_by' => Auth::id(),
                        'rejected_at' => now(),
                    ]))
                    ->visible(function (ProcurementProposal $record): bool {
                        $user = Auth::user();
                        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
                        return !$isSekolahRole && in_array($record->status, ['submitted', 'under_review']);
                    }),
                \Filament\Actions\EditAction::make()
                    ->visible(function (ProcurementProposal $record): bool {
                        $user = Auth::user();
                        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
                        // Sekolah hanya bisa edit jika draft atau rejected
                        if ($isSekolahRole) {
                            return in_array($record->status, ['draft', 'rejected']);
                        }
                        return true;
                    }),
                \Filament\Actions\DeleteAction::make()
                    ->visible(function (ProcurementProposal $record): bool {
                        $user = Auth::user();
                        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
                        // Sekolah hanya bisa hapus jika draft atau rejected
                        if ($isSekolahRole) {
                            return in_array($record->status, ['draft', 'rejected']);
                        }
                        return true;
                    }),
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
