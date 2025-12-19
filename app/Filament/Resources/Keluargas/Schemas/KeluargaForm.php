<?php

namespace App\Filament\Resources\Keluargas\Schemas;

use App\Models\RT;
use App\Models\RW;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rw_id')
                    ->label('RW')
                    ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                        $user = Auth::user();
                        if ($user->isRW() || $user->isRT()) {
                            $query->where('id', $user->rw_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(RW $rw) => "RW {$rw->nomor}")
                    ->afterStateUpdated(fn(callable $set) => $set('rt_id', null))
                    ->live()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->hint('Pilih RW sesuai data wilayah')
                    ->hintIcon('heroicon-m-map')
                    ->hintIconTooltip('RW adalah singkatan dari Rukun Warga, yaitu pembagian wilayah administratif di bawah kelurahan.'),

                Select::make('rt_id')
                    ->label('RT')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->relationship('rt', 'nomor', modifyQueryUsing: function (Builder $query, $get) {
                        $user = Auth::user();
                        $query->where('rw_id', $get('rw_id'));

                        if ($user->isRT()) {
                            $query->where('id', $user->rt_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(RT $rt) => "RT {$rt->nomor}")
                    ->hint('Pilih RT berdasarkan RW yang telah dipilih')
                    ->hintIcon('heroicon-m-map-pin')
                    ->hintIconTooltip('RT adalah singkatan dari Rukun Tetangga, yaitu pembagian wilayah yang lebih kecil di dalam RW.'),

                TextInput::make('no_kk')
                    ->label('Nomor KK')
                    ->length(16)
                    ->maxLength(16)
                    ->extraInputAttributes([
                        'inputmode' => 'numeric',
                        'pattern' => '[0-9]*',
                        'oninput' => "this.value = this.value.replace(/\\D/g,'').slice(0,16)",
                    ])
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->columnSpanFull()
                    ->hintIcon('heroicon-m-identification')
                    ->hintIconTooltip('Nomor KK terdiri dari 16 digit angka yang tercantum pada dokumen Kartu Keluarga.'),

                Textarea::make('alamat')
                    ->label('Alamat')
                    ->required()
                    ->columnSpanFull()
                    ->hintIcon('heroicon-m-home')
                    ->hintIconTooltip('Alamat pada Kartu Keluarga'),

                Select::make('kepala_id')
                    ->label('Kepala Keluarga')
                    ->relationship('kepala', 'nama')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nama} — {$record->nik}")
                    ->preload()
                    ->searchable()
                    ->hiddenOn('create')
                    ->columnSpanFull()
                    ->hintIcon('heroicon-m-user-circle')
                    ->hintIconTooltip('Kepala keluarga adalah anggota keluarga yang tercantum sebagai penanggung jawab di KK.')
                    ->helperText('Pilih kepala keluarga yang terdaftar di sistem. Jika belum ada, daftarkan di data penduduk atau tetapkan melalui anggota keluarga dengan memilih status "Kepala" pada data penduduk.'),
            ]);
    }
}
