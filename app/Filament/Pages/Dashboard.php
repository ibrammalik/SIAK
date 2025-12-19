<?php

namespace App\Filament\Pages;

use App\Models\Kelurahan;
use App\Models\RT;
use App\Models\RW;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
  use HasFiltersAction;

  protected function getHeaderActions(): array
  {
    // ketua rt = rt wilayahnya atau hide saja filter dasbor
    if (Auth::user()->isRT()) return [];

    return [
      FilterAction::make()
        ->schema([
          Select::make('wilayah')
            ->label('Wilayah')
            ->options(function () {
              // todo:
              if (Auth::user()->isSuperAdmin() || Auth::user()->isKelurahan()) {
                // admin kelurahan & super admin = full access
                return [
                  'kelurahan' => 'Kelurahan',
                  'rw' => 'RW',
                  'rt' => 'RT',
                ];
              } elseif (Auth::user()->isRW()) {
                // ketua rw = rw & rt wilayahnya
                return [
                  'rw' => 'RW',
                  'rt' => 'RT',
                ];
              } else {
                return [];
              }
            })
            ->live()
            ->afterStateUpdated(function ($set) {
              $set('kelurahan', null);
              $set('rw', null);
              $set('rt', null);
            })
            ->afterStateHydrated(fn($set) => $set('wilayah', null))
            ->native(false)
            ->columnSpanFull()
            ->required(),

          Select::make('kelurahan')
            ->label('Kelurahan')
            ->options(Kelurahan::query()->pluck('nama', 'id'))
            ->afterStateHydrated(fn($set) => $set('kelurahan', null))
            ->native(false)
            ->preload()
            ->visibleJs(<<<'JS'
              $get('wilayah') === 'kelurahan'
              JS)
            ->columnSpanFull(),

          Select::make('rw')
            ->columnSpanFull()
            // todo: sesuai scope wilayahnya bagi ketua rw
            ->options(RW::query()
              ->when(
                Auth::user()->isRW(),
                fn($query) => $query->where('id', Auth::user()->rw_id)
              )
              ->pluck('nomor', 'id'))
            ->native(false)
            ->preload()
            ->visibleJs(<<<'JS'
              $get('wilayah') === 'rw' || $get('wilayah') === 'rt'
              JS)
            ->label('RW')
            ->live()
            ->afterStateUpdated(fn($set) => $set('rt', null))
            ->afterStateHydrated(fn($set) => $set('rw', null)),

          Select::make('rt')
            ->label('RT')
            ->columnSpanFull()
            ->native(false)
            ->options(function ($get) {
              if ($get('rw') === null) return [];
              // todo: sesuai scope wilayahnya bagi ketua rw
              return RT::query()->where('rw_id', $get('rw'))->pluck('nomor', 'id');
            })
            ->preload()
            ->visibleJs(<<<'JS'
              $get('wilayah') === 'rt'
              JS)
            ->afterStateHydrated(fn($set) => $set('rw', null)),
        ])
    ];
  }
}
