<?php

namespace App\Filament\Resources\Penduduks\Pages;

use App\Filament\Imports\PendudukImporter;
use App\Filament\Resources\Penduduks\PendudukResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListPenduduks extends ListRecords
{
    protected static string $resource = PendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Penduduk')
                ->icon('heroicon-o-plus')
                ->color('success'),

            ImportAction::make('import_penduduk')
                ->label('Import Penduduk')
                ->color('info')
                ->icon('heroicon-o-document-arrow-up')
                ->importer(PendudukImporter::class)
                ->modalDescription(function () {
                    return new HtmlString('
                        <a class="text-sm font-medium text-primary-400 hover:underline cursor-pointer"
                            href="' . route('download.contoh-import') . '"
                        >
                            Download contoh file dan tutorial import penduduk.
                        </a>
                    ');
                }),
        ];
    }
}
