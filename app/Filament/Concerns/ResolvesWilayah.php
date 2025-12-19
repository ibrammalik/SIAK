<?php

namespace App\Filament\Concerns;

use App\Models\RT;
use App\Models\RW;
use Illuminate\Support\Facades\Auth;

trait ResolvesWilayah
{
  protected function resolveWilayah(): array
  {
    $user = Auth::user();
    $filters = $this->pageFilters ?? [];

    $wilayah = $filters['wilayah'] ?? null;
    $rwId = $filters['rw'] ?? null;
    $rtId = $filters['rt'] ?? null;

    // default aman
    $state = [
      'wilayah' => 'kelurahan',
      'rw' => null,
      'rt' => null,
    ];

    // SUPER ADMIN / KELURAHAN
    if ($user->isSuperAdmin() || $user->isKelurahan()) {
      if ($wilayah === 'rw' && $rwId && RW::whereKey($rwId)->exists()) {
        return [
          'wilayah' => 'rw',
          'rw' => RW::find($rwId),
          'rt' => null,
        ];
      }

      if ($wilayah === 'rt' && $rtId) {
        $rt = RT::with('rw')->find($rtId);
        if ($rt) {
          return [
            'wilayah' => 'rt',
            'rw' => $rt->rw,
            'rt' => $rt,
          ];
        }
      }

      return $state;
    }

    // RW
    if ($user->isRW()) {
      if ($wilayah === 'rt' && $rtId) {
        $rt = RT::where('rw_id', $user->rw_id)->find($rtId);
        if ($rt) {
          return [
            'wilayah' => 'rt',
            'rw' => $user->rw,
            'rt' => $rt,
          ];
        }
      }

      return [
        'wilayah' => 'rw',
        'rw' => $user->rw,
        'rt' => null,
      ];
    }

    // RT
    if ($user->isRT()) {
      return [
        'wilayah' => 'rt',
        'rw' => $user->rt->rw,
        'rt' => $user->rt,
      ];
    }

    return $state;
  }
}
