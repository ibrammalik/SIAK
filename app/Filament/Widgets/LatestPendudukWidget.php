<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesWilayah;
use App\Models\Penduduk;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPendudukWidget extends TableWidget
{
    use InteractsWithPageFilters, ResolvesWilayah;

    protected static bool $isLazy = false;
    protected static ?string $heading = 'Penduduk Terbaru';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $state = $this->resolveWilayah();

                $query = Penduduk::query();

                if ($state['wilayah'] === 'rw') {
                    $query->where('rw_id', $state['rw']->id);
                }

                if ($state['wilayah'] === 'rt') {
                    $query->where('rt_id', $state['rt']->id);
                }

                return $query->latest()->limit(6);
            })
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn($state) => $state === 'L' ? 'info' : 'pink'),

                TextColumn::make('rw.nomor')
                    ->label('RW')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('rt.nomor')
                    ->label('RT')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime('d F Y')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([])

            ->headerActions([])

            ->recordActions([])

            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
