<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SarprasTypeResource\Pages;
use App\Models\SarprasType;
use App\Models\SarprasCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SarprasTypeResource extends Resource
{
    protected static ?string $model = SarprasType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tipe SARPRAS';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = '';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tipe SARPRAS')
                    ->description('Masukkan detail tipe SARPRAS')
                    ->columns(2)
                    ->schema([
                        Select::make('sarpras_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        TextInput::make('code')
                            ->label('Kode Tipe')
                            ->placeholder('Contoh: RK01')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        TextInput::make('name')
                            ->label('Nama Tipe')
                            ->placeholder('Contoh: Ruang Kelas')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('Contoh: unit, buah, ruang')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Tipe')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('sekolah_sarpras_count')
                    ->label('Digunakan')
                    ->counts('sekolahSarpras')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('sarpras_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
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
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListSarprasTypes::route('/'),
            'create' => Pages\CreateSarprasType::route('/create'),
            'edit' => Pages\EditSarprasType::route('/{record}/edit'),
        ];
    }
}
