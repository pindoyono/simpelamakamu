<?php

namespace App\Filament\Resources\Sekolahs;

use App\Filament\Resources\Sekolahs\Pages\CreateSekolah;
use App\Filament\Resources\Sekolahs\Pages\EditSekolah;
use App\Filament\Resources\Sekolahs\Pages\ListSekolahs;
use App\Filament\Resources\Sekolahs\Schemas\SekolahForm;
use App\Filament\Resources\Sekolahs\Tables\SekolahsTable;
use App\Models\Sekolah;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SekolahResource extends Resource
{
    protected static ?string $model = Sekolah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Data Sekolah';

    protected static ?string $modelLabel = 'Sekolah';

    protected static ?string $pluralModelLabel = 'Data Sekolah';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        $user = Auth::user();
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            return null;
        }
        return 'Master Data';
    }

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Filter untuk role sekolah - hanya tampilkan sekolah miliknya
        if ($user && $user->hasRole('sekolah') && !$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            $sekolahIds = $user->sekolahs()->pluck('sekolahs.id')->toArray();
            $query->whereIn('id', $sekolahIds);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return SekolahForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SekolahsTable::configure($table);
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
            'index' => ListSekolahs::route('/'),
            'create' => CreateSekolah::route('/create'),
            'edit' => EditSekolah::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['npsn', 'nama', 'kepala_sekolah'];
    }
}
