<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesWilayah;
use App\Models\Penduduk;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DistribusiUmurChart extends ChartWidget
{
    use InteractsWithPageFilters, ResolvesWilayah;

    protected ?string $heading = 'Distribusi Umur';
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

        $ageGroups = [
            '0-4',
            '5-9',
            '10-14',
            '15-19',
            '20-24',
            '25-29',
            '30-34',
            '35-39',
            '40-44',
            '45-49',
            '50-54',
            '55-59',
            '60-64',
            '65+',
        ];

        $raw = $query
            ->selectRaw("
            CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 4 THEN '0-4'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN '5-9'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN '10-14'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN '15-19'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 20 AND 24 THEN '20-24'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 25 AND 29 THEN '25-29'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 30 AND 34 THEN '30-34'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 35 AND 39 THEN '35-39'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 40 AND 44 THEN '40-44'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 49 THEN '45-49'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 50 AND 54 THEN '50-54'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 55 AND 59 THEN '55-59'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 60 AND 64 THEN '60-64'
                ELSE '65+'
            END AS kategori,
            COUNT(*) AS total
        ")
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        $normalized = [];
        foreach ($ageGroups as $label) {
            $normalized[$label] = $raw[$label] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penduduk',
                    'data' => array_values($normalized),
                ],
            ],
            'labels' => array_keys($normalized),
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
