<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentUniformResource\Pages;
use App\Models\StudentUniform;
use App\Models\AcademicPeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class StudentUniformResource extends Resource
{
    protected static ?string $model = StudentUniform::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Ukuran Seragam';

    protected static ?string $modelLabel = 'Ukuran Seragam Siswa';

    protected static ?string $pluralModelLabel = 'Ukuran Seragam Siswa';

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        $isSekolahRole = $user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin');
        $userSekolahId = $isSekolahRole ? $user->sekolahs()->first()?->id : null;

        return $schema
            ->components([
                Section::make('Data Siswa')
                    ->icon('heroicon-o-user')
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
                                        $active = AcademicPeriod::where('is_active', true)->first();
                                        return $active ? [$active->id => $active->year . ' ✓ (Aktif)'] : [];
                                    })
                                    ->required()
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn () => AcademicPeriod::where('is_active', true)->first()?->id),

                                TextInput::make('nama_siswa')
                                    ->label('Nama Siswa')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('nisn')
                                    ->label('NISN')
                                    ->maxLength(20)
                                    ->placeholder('Nomor Induk Siswa Nasional')
                                    ->unique(
                                        table: 'student_uniforms',
                                        column: 'nisn',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, $get) => $rule->where('academic_period_id', $get('academic_period_id')),
                                    )
                                    ->validationMessages([
                                        'unique' => 'NISN ini sudah terdaftar pada tahun ajaran ini.',
                                    ]),

                                Select::make('jenis_kelamin')
                                    ->label('Laki-laki / Perempuan')
                                    ->required()
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->native(false),

                                Select::make('kelas')
                                    ->label('Kelas')
                                    ->required()
                                    ->options(StudentUniform::getKelasOptions())
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Size / Ukuran')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('ukuran_baju')
                                    ->label('Ukuran Baju')
                                    ->required()
                                    ->options(StudentUniform::getSizeOptions())
                                    ->native(false),

                                Select::make('ukuran_celana_rok')
                                    ->label('Ukuran Celana/Rok')
                                    ->required()
                                    ->options(StudentUniform::getSizeOptions())
                                    ->native(false),

                                Select::make('ukuran_sepatu')
                                    ->label('Ukuran Sepatu')
                                    ->required()
                                    ->options(StudentUniform::getSepatuSizeOptions())
                                    ->native(false),
                            ]),
                    ]),
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
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('academicPeriod.year')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'info',
                        'P' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => $state,
                    }),

                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('ukuran_baju')
                    ->label('Baju')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('ukuran_celana_rok')
                    ->label('Celana/Rok')
                    ->badge()
                    ->color('success'),

                TextColumn::make('ukuran_sepatu')
                    ->label('Sepatu')
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('nama_siswa')
            ->filters([
                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->relationship('sekolah', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('academic_period_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicPeriod::orderBy('year', 'desc')->pluck('year', 'id'))
                    ->default(fn () => AcademicPeriod::where('is_active', true)->first()?->id),
                SelectFilter::make('kelas')
                    ->options(StudentUniform::getKelasOptions()),
                SelectFilter::make('jenis_kelamin')
                    ->label('L/P')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            $sekolahIds = $user->sekolahs()->pluck('sekolahs.id');
            $query->whereIn('sekolah_id', $sekolahIds);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentUniforms::route('/'),
            'create' => Pages\CreateStudentUniform::route('/create'),
            'edit' => Pages\EditStudentUniform::route('/{record}/edit'),
            'import' => Pages\ImportStudentUniform::route('/import'),
        ];
    }
}
