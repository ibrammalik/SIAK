<?php

namespace App\Filament\Resources\KategoriUsahas;

use App\Filament\Resources\KategoriUsahas\Pages\CreateKategoriUsaha;
use App\Filament\Resources\KategoriUsahas\Pages\EditKategoriUsaha;
use App\Filament\Resources\KategoriUsahas\Pages\ListKategoriUsahas;
use App\Filament\Resources\KategoriUsahas\Pages\ViewKategoriUsaha;
use App\Filament\Resources\KategoriUsahas\RelationManagers\SubkategoriUsahaRelationManager;
use App\Filament\Resources\KategoriUsahas\Schemas\KategoriUsahaForm;
use App\Filament\Resources\KategoriUsahas\Tables\KategoriUsahasTable;
use App\Models\KategoriUsaha;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KategoriUsahaResource extends Resource
{
    protected static ?string $model = KategoriUsaha::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kategori Usaha';
    protected static ?string $pluralModelLabel = 'Kategori Usaha';
    protected static ?string $modelLabel = 'Kategori Usaha';

    public static function form(Schema $schema): Schema
    {
        return KategoriUsahaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriUsahasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SubkategoriUsahaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKategoriUsahas::route('/'),
            'create' => CreateKategoriUsaha::route('/create'),
            'edit' => EditKategoriUsaha::route('/{record}/edit'),
            'view' => ViewKategoriUsaha::route('/{record}'),
        ];
    }
}
