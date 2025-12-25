<?php

namespace App\Filament\Resources\Penduduks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenduduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),

                TextColumn::make('keluarga.no_kk')
                    ->label('Nomor KK')
                    ->sortable(),

                TextColumn::make('rt.nomor')
                    ->label('RT')
                    ->sortable(),

                TextColumn::make('rw.nomor')
                    ->label('RW')
                    ->sortable(),

                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Lahir')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('agama')
                    ->label('Agama')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pendidikan.name')
                    ->label('Pendidikan')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status_perkawinan')
                    ->label('Status Perkawinan')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pekerjaan.name')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status_kependudukan')
                    ->label('Status Kependudukan')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('shdk')
                    ->label('Status Dalam Keluarga')
                    ->badge()
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
                Filter::make('usia')
                    ->schema([
                        TextInput::make('usia_min')->numeric()->label('Usia Minimal'),
                        TextInput::make('usia_max')->numeric()->label('Usia Maksimal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['usia_min'],
                                fn($q, $usiaMin) =>
                                $q->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$usiaMin])
                            )
                            ->when(
                                $data['usia_max'],
                                fn($q, $usiaMax) =>
                                $q->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?', [$usiaMax])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['usia_min'] ?? null) {
                            $indicators[] = 'Usia ≥ ' . $data['usia_min'];
                        }
                        if ($data['usia_max'] ?? null) {
                            $indicators[] = 'Usia ≤ ' . $data['usia_max'];
                        }

                        return $indicators;
                    }),
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
