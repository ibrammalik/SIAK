<?php

namespace App\Filament\Resources\Pekerjaans\Pages;

use App\Filament\Resources\Pekerjaans\PekerjaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePekerjaans extends ManageRecords
{
    protected static string $resource = PekerjaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
