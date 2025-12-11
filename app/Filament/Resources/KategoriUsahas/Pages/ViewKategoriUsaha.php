<?php

namespace App\Filament\Resources\KategoriUsahas\Pages;

use App\Filament\Resources\KategoriUsahas\KategoriUsahaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriUsaha extends ViewRecord
{
    protected static string $resource = KategoriUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
