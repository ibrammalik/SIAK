<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesWilayah;
use App\Models\KategoriPendidikan;
use App\Models\Penduduk;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DistribusiPendidikanChart extends ChartWidget
{
    use InteractsWithPageFilters, ResolvesWilayah;

    protected ?string $heading = 'Distribusi Pendidikan';
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $state = $this->resolveWilayah();

        $query = Penduduk::query();

        if ($state['wilayah'] === 'rw') {
            $query->where('rw_id', $state['rw']->id);
        }

        if ($state['wilayah'] === 'rt') {
            $query->where('rt_id', $state['rt']->id);
        }

        // Ambil data dari database
        $rawData = DB::table('kategori_pendidikans as kp')
            ->leftJoin('penduduks as p', 'p.pendidikan_id', '=', 'kp.id')
            ->when($state['wilayah'] === 'rw', function ($q) use ($state) {
                $q->where('p.rw_id', $state['rw']->id);
            })
            ->when($state['wilayah'] === 'rt', function ($q) use ($state) {
                $q->where('p.rt_id', $state['rt']->id);
            })
            ->select(
                'kp.name as pendidikan',
                DB::raw('COUNT(p.id) as total')
            )
            ->groupBy('kp.name')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // Normalisasi berdasarkan Enum Pendidikan
        $labels = [];
        $values = [];

        foreach (KategoriPendidikan::all() as $pendidikan) {
            $labels[] = $pendidikan->name;
            $values[] = $rawData[$pendidikan->name] ?? 0;
        }

        $belumDiisi = DB::table('penduduks')
            ->whereNull('pendidikan_id')
            ->when(
                $state['wilayah'] === 'rw',
                fn($q) =>
                $q->where('rw_id', $state['rw']->id)
            )
            ->when(
                $state['wilayah'] === 'rt',
                fn($q) =>
                $q->where('rt_id', $state['rt']->id)
            )
            ->count();

        $labels[] = "Belum Diisi";
        $values[] = $belumDiisi;

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penduduk',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
