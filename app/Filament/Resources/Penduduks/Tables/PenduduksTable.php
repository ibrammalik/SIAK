<?php

namespace App\Filament\Resources\Penduduks\Tables;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\Shdk;
use App\Enums\StatusKependudukan;
use App\Enums\StatusPerkawinan;
use App\Models\RT;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
                SelectFilter::make('rw')
                    ->label('RW')
                    ->multiple()
                    ->relationship('rw', 'nomor', hasEmptyOption: true, modifyQueryUsing: function (Builder $query) {
                        $user = Auth::user();
                        if ($user->isRW() || $user->isRT()) {
                            $query->where('id', $user->rw_id);
                        }
                    }),

                SelectFilter::make('rt')
                    ->label('RT')
                    ->attribute('rt_id')
                    ->multiple()
                    ->options(function () {
                        $user = Auth::user();
                        $rts = RT::query()
                            ->select('rts.*') // ensure we only select RT fields
                            ->join('rws', 'rws.id', '=', 'rts.rw_id')
                            ->when($user->isRW(), fn($q) => $q->where('rws.id', $user->rw_id))
                            ->when($user->isRT(), fn($q) => $q->where('rts.id', $user->rt_id))
                            ->orderBy('rws.nomor')   // RW ascending
                            ->orderBy('rts.nomor')   // RT ascending
                            ->with('rw')             // eager load RW for labels
                            ->get();

                        return $rts->mapWithKeys(fn($rt) => [
                            $rt->id => 'RT ' . $rt->nomor . ' / RW ' . $rt->rw->nomor,
                        ])->toArray();
                    }),

                SelectFilter::make('keluarga')
                    ->label('No. KK')
                    ->multiple()
                    ->relationship('keluarga', 'no_kk', hasEmptyOption: true, modifyQueryUsing: function (Builder $query) {
                        $user = Auth::user();

                        if ($user->isRW()) $query->where('rw_id', $user->rw_id);
                        if ($user->isRT()) $query->where('rt_id', $user->rt_id);
                    }),

                SelectFilter::make('jenis_kelamin')
                    ->multiple()
                    ->options(JenisKelamin::options()),

                SelectFilter::make('agama')
                    ->multiple()
                    ->options(Agama::options()),

                SelectFilter::make('pendidikan')
                    ->multiple()
                    ->relationship('pendidikan', 'name', hasEmptyOption: true),

                SelectFilter::make('status_perkawinan')
                    ->multiple()
                    ->options(StatusPerkawinan::options()),

                SelectFilter::make('pekerjaan')
                    ->multiple()
                    ->relationship('pekerjaan', 'name', hasEmptyOption: true),

                SelectFilter::make('status_kependudukan')
                    ->multiple()
                    ->options(StatusKependudukan::options()),

                SelectFilter::make('shdk')
                    ->multiple()
                    ->options(Shdk::options()),

                Filter::make('usia_min')
                    ->schema([TextInput::make('usia_min')->numeric()->label('Minimal')])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['usia_min'],
                            fn($q, $usiaMin) => $q->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$usiaMin])
                        );
                    })
                    ->indicateUsing(fn(array $data) => !empty($data['usia_min']) ? ['Usia ≥ ' . $data['usia_min']] : []),

                Filter::make('usia_max')
                    ->schema([TextInput::make('usia_max')->numeric()->label('Maximal')])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['usia_max'],
                            fn($q, $usiaMax) => $q->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$usiaMax])
                        );
                    })
                    ->indicateUsing(fn(array $data) => !empty($data['usia_max']) ? ['Usia ≤ ' . $data['usia_max']] : []),

                // Filter for Tanggal Lahir Minimal
                Filter::make('tanggal_lahir_min')
                    ->schema([DatePicker::make('tanggal_lahir_min')->label('Minimal')])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['tanggal_lahir_min'],
                            fn($q, $min) => $q->where('tanggal_lahir', '>=', $min)
                        );
                    })
                    ->indicateUsing(fn(array $data) => !empty($data['tanggal_lahir_min']) ? ['Lahir ≥ ' . $data['tanggal_lahir_min']] : []),

                // Filter for Tanggal Lahir Maksimal
                Filter::make('tanggal_lahir_max')
                    ->schema([DatePicker::make('tanggal_lahir_max')->label('Maksimal')])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['tanggal_lahir_max'],
                            fn($q, $max) => $q->where('tanggal_lahir', '<=', $max)
                        );
                    })
                    ->indicateUsing(fn(array $data) => !empty($data['tanggal_lahir_max']) ? ['Lahir ≤ ' . $data['tanggal_lahir_max']] : []),

            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(3)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->slideOver()
                    ->button()
                    ->label('Filter'),
            )
            ->filtersFormSchema(fn(array $filters): array => [
                Section::make('Wilayah RT / RW')
                    ->schema([
                        $filters['rt'],
                        $filters['rw'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Keluarga')
                    ->schema([
                        $filters['keluarga']
                    ])
                    ->columnSpanFull(),

                Section::make('Data Kependudukan')
                    ->schema([
                        $filters['jenis_kelamin'],
                        $filters['agama'],
                        $filters['pendidikan'],
                        $filters['status_perkawinan'],
                        $filters['pekerjaan'],
                        $filters['status_kependudukan'],
                        $filters['shdk'],
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Usia')
                    ->schema([
                        $filters['usia_min'],
                        $filters['usia_max'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Tanggal Lahir')
                    ->schema([
                        $filters['tanggal_lahir_min'],
                        $filters['tanggal_lahir_max'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
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
