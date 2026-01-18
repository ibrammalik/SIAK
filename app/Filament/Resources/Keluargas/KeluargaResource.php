<?php

namespace App\Filament\Resources\Keluargas;

use App\Enums\Shdk;
use App\Filament\Resources\Keluargas\Pages\CreateKeluarga;
use App\Filament\Resources\Keluargas\Pages\EditKeluarga;
use App\Filament\Resources\Keluargas\Pages\ListKeluargas;
use App\Filament\Resources\Keluargas\Pages\ViewKeluarga;
use App\Filament\Resources\Keluargas\RelationManagers\PenduduksRelationManager;
use App\Filament\Resources\Keluargas\Schemas\KeluargaForm;
use App\Filament\Resources\Keluargas\Schemas\KeluargaInfolist;
use App\Filament\Resources\Keluargas\Tables\KeluargasTable;
use App\Models\Keluarga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class KeluargaResource extends Resource
{
    protected static ?string $model = Keluarga::class;
    protected static ?string $recordTitleAttribute = 'no_kk';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;
    protected static string | UnitEnum | null $navigationGroup = 'Kependudukan';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Keluarga';
    protected static ?string $pluralModelLabel = 'Keluarga';
    protected static ?string $modelLabel = 'Keluarga';

    public static function form(Schema $schema): Schema
    {
        return KeluargaForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return KeluargaInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return KeluargasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PenduduksRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeluargas::route('/'),
            'create' => CreateKeluarga::route('/create'),
            'view' => ViewKeluarga::route('/{record}'),
            'edit' => EditKeluarga::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Base query with eager-loading
        $query = parent::getEloquentQuery()
            ->with([
                'kepala', // normal kepala
                'penduduks' => fn($q) => $q->where('shdk', Shdk::Kepala) // fallback
            ]);

        if ($user->isSuperAdmin() || $user->isKelurahan()) return $query;
        if ($user->isRW()) return $query->where('rw_id', $user->rw_id);
        if ($user->isRT()) return $query->where('rt_id', $user->rt_id);
        return $query;
    }
}
