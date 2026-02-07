<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicPeriodResource\Pages;
use App\Models\AcademicPeriod;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\Builder;

class AcademicPeriodResource extends Resource
{
    protected static ?string $model = AcademicPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?string $modelLabel = 'Tahun Ajaran';

    protected static ?string $pluralModelLabel = 'Tahun Ajaran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tahun Ajaran')
                    ->description('Masukkan detail tahun ajaran')
                    ->columns(2)
                    ->schema([
                        TextInput::make('year')
                            ->label('Tahun Ajaran')
                            ->placeholder('2024/2025')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(9)
                            ->helperText('Format: 2024/2025'),

                        Select::make('semester')
                            ->label('Semester')
                            ->required()
                            ->options([
                                'ganjil' => 'Ganjil (Odd)',
                                'genap' => 'Genap (Even)'
                            ])
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Hanya satu tahun ajaran yang bisa aktif')
                            ->default(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap'
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ]),
            ])
            ->actions([
                Action::make('set_active')
                    ->label('Set Aktif')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Tahun Ajaran')
                    ->modalDescription('Apakah Anda yakin ingin mengaktifkan tahun ajaran ini? Tahun ajaran lain yang aktif akan dinonaktifkan.')
                    ->action(function (AcademicPeriod $record): void {
                        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
                        $record->update(['is_active' => true]);
                    })
                    ->visible(fn (AcademicPeriod $record): bool => !$record->is_active),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('year', 'desc');
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
            'index' => Pages\ListAcademicPeriods::route('/'),
            'create' => Pages\CreateAcademicPeriod::route('/create'),
            'edit' => Pages\EditAcademicPeriod::route('/{record}/edit'),
        ];
    }
}
