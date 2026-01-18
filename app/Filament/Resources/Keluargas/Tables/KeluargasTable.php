<?php

namespace App\Filament\Resources\Keluargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kk')
                    ->label('Nomor KK')
                    ->searchable(),

                TextColumn::make('rw.nomor')
                    ->label('RW')
                    ->sortable(),

                TextColumn::make('rt.nomor')
                    ->label('RT')
                    ->sortable(),

                TextColumn::make('kepala.nama')
                    ->label('Kepala')
                    ->default('-')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        //Pakai relationship kepala jika kepala_id tidak null
                        if ($record->kepala_id && $record->kepala) {
                            return $record->kepala->nama;
                        }

                        // Selain itu, pakai penduduk dengan shdk = Kepala yg sudah 
                        // di eager load di getEloquentQuery di KeluargaResource
                        $pendudukKepala = $record->penduduks->first();
                        return $pendudukKepala?->nama ?? '-';
                    }),

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
