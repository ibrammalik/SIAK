<?php

namespace App\Filament\Resources\KategoriFasilitas\Pages;

use App\Filament\Resources\KategoriFasilitas\KategoriFasilitasResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriFasilitas extends ViewRecord
{
    protected static string $resource = KategoriFasilitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
