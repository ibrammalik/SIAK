<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesWilayah;
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
        $rawData = $query
            ->select('pendidikan', DB::raw('COUNT(*) as total'))
            ->groupBy('pendidikan')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // Normalisasi berdasarkan Enum Pendidikan
        $labels = [];
        $values = [];

        foreach (\App\Enums\Pendidikan::values() as $pendidikan) {
            $labels[] = $pendidikan;
            $values[] = $rawData[$pendidikan] ?? 0;
        }

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
