<?php

namespace App\Filament\Resources\KategoriPekerjaans\Pages;

use App\Filament\Resources\KategoriPekerjaans\KategoriPekerjaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriPekerjaans extends ManageRecords
{
    protected static string $resource = KategoriPekerjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
