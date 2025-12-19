<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesWilayah;
use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\RT;
use App\Models\RW;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    use InteractsWithPageFilters, ResolvesWilayah;

    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        $kelurahan = Kelurahan::first();
        $state = $this->resolveWilayah();

        return match ($state['wilayah']) {
            'rt' => "Statistik RT {$state['rt']->nomor} / RW {$state['rw']->nomor}, Kelurahan {$kelurahan->nama}",
            'rw' => "Statistik RW {$state['rw']->nomor}, Kelurahan {$kelurahan->nama}",
            default => "Statistik Kelurahan {$kelurahan->nama}",
        };
    }

    protected function getDescription(): ?string
    {
        return 'Menampilkan ikhtisar statistik wilayah berdasarkan filter dan peran pengguna.';
    }

    protected function getStats(): array
    {
        $state = $this->resolveWilayah();

        $penduduk = Penduduk::query();
        $keluarga = Keluarga::query();
        $rt = RT::query();
        $rw = RW::query();

        if ($state['wilayah'] === 'rw') {
            $penduduk->where('rw_id', $state['rw']->id);
            $keluarga->where('rw_id', $state['rw']->id);
            $rt->where('rw_id', $state['rw']->id);
        }

        if ($state['wilayah'] === 'rt') {
            $penduduk->where('rt_id', $state['rt']->id);
            $keluarga->where('rt_id', $state['rt']->id);
        }

        // Hitung data umum
        $totalPenduduk = $penduduk->count();
        $totalKeluarga = $keluarga->count();
        $totalRw = $rw->count();
        $totalRt = $rt->count();

        $laki = (clone $penduduk)->where('jenis_kelamin', 'L')->count();
        $perempuan = (clone $penduduk)->where('jenis_kelamin', 'P')->count();

        $umurSekarang = now();
        $lansia = (clone $penduduk)
            ->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) >= 60', [$umurSekarang])
            ->count();
        $chart = [1, 1];

        return match ($state['wilayah']) {

            'rt' => [
                Stat::make('Laki-laki / Perempuan', "{$laki} / {$perempuan}")
                    ->icon('heroicon-o-user-group')
                    ->chart($chart)
                    ->color('success')
                    ->description('Jenis Kelamin')
                    ->descriptionIcon('heroicon-o-user-group'),

                Stat::make('Total KK', $totalKeluarga)
                    ->icon('heroicon-o-users')
                    ->chart($chart)
                    ->color('info')
                    ->description('Kepala Keluarga')
                    ->descriptionIcon('heroicon-o-users'),

                Stat::make('Total Penduduk', $totalPenduduk)
                    ->icon('heroicon-o-user-group')
                    ->chart($chart)
                    ->color('primary')
                    ->description('Jumlah warga')
                    ->descriptionIcon('heroicon-o-user-group'),

                Stat::make('Lansia (≥60)', $lansia)
                    ->icon('heroicon-o-user')
                    ->chart($chart)
                    ->color('warning')
                    ->description('Lanjut usia')
                    ->descriptionIcon('heroicon-o-user'),
            ],

            'rw' => [
                Stat::make('Total RT', $totalRt)
                    ->icon('heroicon-o-home-modern')
                    ->chart($chart)
                    ->color('warning')
                    ->description('Jumlah RT di RW ini')
                    ->descriptionIcon('heroicon-o-home-modern'),

                Stat::make('Total KK', $totalKeluarga)
                    ->icon('heroicon-o-user-group')
                    ->chart($chart)
                    ->color('info')
                    ->description('Jumlah KK di RW ini')
                    ->descriptionIcon('heroicon-o-user-group'),

                Stat::make('Laki-laki / Perempuan', "{$laki} / {$perempuan}")
                    ->icon('heroicon-o-users')
                    ->chart($chart)
                    ->color('success')
                    ->description('Gender di RW ini')
                    ->descriptionIcon('heroicon-o-users'),

                Stat::make('Total Penduduk', $totalPenduduk)
                    ->icon('heroicon-o-user-group')
                    ->chart($chart)
                    ->color('primary')
                    ->description('Jumlah warga RW ini')
                    ->descriptionIcon('heroicon-o-user-group'),
            ],

            default => [
                Stat::make('Total RW', $totalRw)
                    ->icon('heroicon-o-building-office-2')
                    ->chart($chart)
                    ->color('success')
                    ->description('Rukun Warga')
                    ->descriptionIcon('heroicon-o-building-office-2'),

                Stat::make('Total RT', $totalRt)
                    ->icon('heroicon-o-home-modern')
                    ->chart($chart)
                    ->color('warning')
                    ->description('Rukun Tetangga')
                    ->descriptionIcon('heroicon-o-home-modern'),

                Stat::make('Total KK', $totalKeluarga)
                    ->icon('heroicon-o-user-group')
                    ->chart($chart)
                    ->color('info')
                    ->description('Kepala Keluarga')
                    ->descriptionIcon('heroicon-o-user-group'),

                Stat::make('Total Penduduk', $totalPenduduk)
                    ->icon('heroicon-o-users')
                    ->chart($chart)
                    ->color('primary')
                    ->description('Jumlah Warga')
                    ->descriptionIcon('heroicon-o-users'),
            ],
        };
    }
}
