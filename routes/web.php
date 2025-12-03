<?php

use App\Http\Controllers\LaporanMonografi;
use App\Livewire\Pages\Beranda;
use App\Livewire\Pages\Infografis;
use App\Livewire\Pages\Kontak;
use App\Livewire\Pages\Monografi;
use App\Livewire\Pages\Peta;
use App\Livewire\Pages\Profil;
use App\Livewire\Pages\RWOnboarding;
use Illuminate\Support\Facades\Route;

Route::get('/', Beranda::class)->name('beranda');
Route::get('/profil', Profil::class)->name('profil');
Route::get('/infografis', Infografis::class)->name('infografis');
// Route::get('/monografi', Monografi::class)->name('monografi');
Route::get('/preview/monografi', [LaporanMonografi::class, 'index'])->name('preview.monografi');
Route::get('/peta', Peta::class)->name('peta');
Route::get('/kontak', Kontak::class)->name('kontak');

Route::get('/rw-onboarding', RWOnboarding::class)->name('rw-onboarding');
