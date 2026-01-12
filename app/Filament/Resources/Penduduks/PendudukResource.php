<?php

namespace App\Filament\Resources\Penduduks;

use App\Filament\Resources\Penduduks\Pages\CreatePenduduk;
use App\Filament\Resources\Penduduks\Pages\EditPenduduk;
use App\Filament\Resources\Penduduks\Pages\ListPenduduks;
use App\Filament\Resources\Penduduks\Pages\ViewPenduduk;
use App\Filament\Resources\Penduduks\Schemas\PendudukForm;
use App\Filament\Resources\Penduduks\Schemas\PendudukInfolist;
use App\Filament\Resources\Penduduks\Tables\PenduduksTable;
use App\Models\Penduduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;
    protected static ?string $recordTitleAttribute = 'nama';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;
    protected static string | UnitEnum | null $navigationGroup = 'Kependudukan';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Input Penduduk';
    protected static ?string $pluralModelLabel = 'Penduduk';
    protected static ?string $modelLabel = 'Penduduk';

    public static function form(Schema $schema): Schema
    {
        return PendudukForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return PendudukInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return PenduduksTable::configure($table);
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
            'index' => ListPenduduks::route('/'),
            'create' => CreatePenduduk::route('/create'),
            'view' => ViewPenduduk::route('/{record}'),
            'edit' => EditPenduduk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->isKelurahan()) return parent::getEloquentQuery();
        if ($user->isRW()) return parent::getEloquentQuery()->where('rw_id', $user->rw_id);
        if ($user->isRT()) return parent::getEloquentQuery()->where('rt_id', $user->rt_id);
        return parent::getEloquentQuery();
    }
}
