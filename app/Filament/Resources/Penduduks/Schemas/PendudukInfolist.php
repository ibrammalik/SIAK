<?php

namespace App\Filament\Resources\Penduduks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PendudukInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('keluarga_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rw_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rt_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nik'),
                TextEntry::make('nama'),
                TextEntry::make('tempat_lahir')
                    ->placeholder('-'),
                TextEntry::make('tanggal_lahir')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('jenis_kelamin')
                    ->badge(),
                TextEntry::make('agama')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('pendidikan')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('status_perkawinan')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('pekerjaan.name')
                    ->placeholder('-'),
                TextEntry::make('status_kependudukan')
                    ->badge(),
                TextEntry::make('shdk')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
