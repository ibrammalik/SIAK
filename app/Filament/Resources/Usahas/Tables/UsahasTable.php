<?php

namespace App\Filament\Resources\Usahas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsahasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('kategoriUsaha.name')
                    ->label('Kategori')
                    ->searchable(),

                TextColumn::make('subkategoriUsaha.name')
                    ->label('Subkategori')
                    ->searchable(),

                TextColumn::make('nama_pemilik')
                    ->searchable(),

                TextColumn::make('nomor_pemilik')
                    ->searchable(),

                TextColumn::make('rw.nomor')
                    ->label('RW')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('rt.nomor')
                    ->label('RT')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('alamat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
