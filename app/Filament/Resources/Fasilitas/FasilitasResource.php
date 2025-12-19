<?php

namespace App\Filament\Resources\Fasilitas;

use App\Filament\Resources\Fasilitas\Pages\CreateFasilitas;
use App\Filament\Resources\Fasilitas\Pages\EditFasilitas;
use App\Filament\Resources\Fasilitas\Pages\ListFasilitas;
use App\Filament\Resources\Fasilitas\Pages\ViewFasilitas;
use App\Filament\Resources\Fasilitas\Schemas\FasilitasForm;
use App\Filament\Resources\Fasilitas\Schemas\FasilitasInfolist;
use App\Filament\Resources\Fasilitas\Tables\FasilitasTable;
use App\Models\Fasilitas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FasilitasResource extends Resource
{
    protected static ?string $model = Fasilitas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    public static string|UnitEnum|null $navigationGroup = 'Data Monografi';

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $slug = 'fasilitas-umum';
    protected static ?string $navigationLabel = 'Fasilitas Umum';
    protected static ?string $pluralModelLabel = 'Fasilitas Umum';
    protected static ?string $modelLabel = 'Fasilitas Umum';

    public static function form(Schema $schema): Schema
    {
        return FasilitasForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return FasilitasInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return FasilitasTable::configure($table);
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
            'index' => ListFasilitas::route('/'),
            'create' => CreateFasilitas::route('/create'),
            'view' => ViewFasilitas::route('/{record}'),
            'edit' => EditFasilitas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if ($user->isSuperAdmin() || $user->isKelurahan()) return parent::getEloquentQuery();
        if ($user->isRW()) return parent::getEloquentQuery()->where('rw_id', $user->rw_id);
        if ($user->isRT()) return parent::getEloquentQuery()->where('rt_id', $user->rt_id);
        return parent::getEloquentQuery();
    }
}
