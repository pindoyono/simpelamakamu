<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SekolahSarprasResource\Pages;
use App\Models\SekolahSarpras;
use App\Models\Sekolah;
use App\Models\SarprasType;
use App\Models\AcademicPeriod;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
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
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class SekolahSarprasResource extends Resource
{
    protected static ?string $model = SekolahSarpras::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen SARPRAS';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data SARPRAS Sekolah';

    protected static ?string $modelLabel = 'Data SARPRAS';

    protected static ?string $pluralModelLabel = 'Data SARPRAS Sekolah';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Sekolah Sarpras')
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

                                        Select::make('sarpras_type_id')
                                            ->label('Tipe SARPRAS')
                                            ->relationship('sarprasType', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false),

                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(0),
                                    ]),
                            ]),
                        Tabs\Tab::make('Kondisi')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('condition_good')
                                            ->label('Baik')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('unit'),

                                        TextInput::make('condition_light_damage')
                                            ->label('Rusak Ringan')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('unit'),

                                        TextInput::make('condition_medium_damage')
                                            ->label('Rusak Sedang')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('unit'),

                                        TextInput::make('condition_heavy_damage')
                                            ->label('Rusak Berat')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('unit'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Status & Catatan')
                            ->icon('heroicon-o-flag')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_verified')
                                            ->label('Terverifikasi')
                                            ->default(false)
                                            ->helperText('Tandai jika data sudah diverifikasi'),

                                        Toggle::make('needs_attention')
                                            ->label('Perlu Perhatian')
                                            ->default(false)
                                            ->helperText('Tandai jika memerlukan tindakan segera'),
                                    ]),

                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(4)
                                    ->columnSpanFull(),
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
                    ->limit(30),

                TextColumn::make('academicPeriod.year')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('sarprasType.name')
                    ->label('Tipe SARPRAS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('condition_good')
                    ->label('Baik')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('condition_light_damage')
                    ->label('RR')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('condition_medium_damage')
                    ->label('RS')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('condition_heavy_damage')
                    ->label('RB')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),

                IconColumn::make('is_verified')
                    ->label('Verifikasi')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('needs_attention')
                    ->label('Perhatian')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->toggleable(),
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
                SelectFilter::make('sarpras_type_id')
                    ->label('Tipe SARPRAS')
                    ->relationship('sarprasType', 'name'),
                SelectFilter::make('is_verified')
                    ->label('Status Verifikasi')
                    ->options([
                        '1' => 'Terverifikasi',
                        '0' => 'Belum Verifikasi'
                    ]),
                SelectFilter::make('needs_attention')
                    ->label('Perlu Perhatian')
                    ->options([
                        '1' => 'Ya',
                        '0' => 'Tidak'
                    ]),
            ])
            ->actions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (SekolahSarpras $record) => $record->update(['is_verified' => true]))
                    ->visible(fn (SekolahSarpras $record): bool => !$record->is_verified),
                Action::make('flag_attention')
                    ->label('Tandai Perhatian')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (SekolahSarpras $record) => $record->update(['needs_attention' => !$record->needs_attention])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_verify')
                        ->label('Verifikasi Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_verified' => true])),
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
            'index' => Pages\ListSekolahSarpras::route('/'),
            'create' => Pages\CreateSekolahSarpras::route('/create'),
            'edit' => Pages\EditSekolahSarpras::route('/{record}/edit'),
        ];
    }
}
