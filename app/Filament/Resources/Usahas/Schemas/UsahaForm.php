<?php

namespace App\Filament\Resources\Usahas\Schemas;

use App\Models\KategoriUsaha;
use App\Models\RT;
use App\Models\RW;
use App\Models\SubkategoriUsaha;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UsahaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kategori_usaha_id')
                    ->label('Kategori')
                    ->relationship('kategoriUsaha', 'name')
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('subkategori_usaha_id', null);
                    })
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required(),
                    ])
                    ->required()
                    ->hintIcon('heroicon-o-information-circle')
                    ->hintIconTooltip('Pilih kategori utama usaha'),

                Select::make('subkategori_usaha_id')
                    ->label('Subkategori')
                    ->relationship(
                        name: 'subkategoriUsaha',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, $get) {
                            $kategoriUsahaId = $get('kategori_usaha_id');
                            if ($kategoriUsahaId !== null) {
                                return $query->where('kategori_usaha_id', $kategoriUsahaId);
                            }
                        }
                    )
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $sub = SubkategoriUsaha::find($state);
                            if ($sub) {
                                $set('kategori_usaha_id', $sub->kategori_usaha_id);
                            }
                        }
                    })
                    ->createOptionForm([
                        Select::make('kategori_usaha_id')
                            ->label('Kategori')
                            ->options(KategoriUsaha::pluck('name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Subkategori')
                            ->required(),
                    ])
                    ->required()
                    ->hintIcon('heroicon-o-information-circle')
                    ->hintIconTooltip('Pilih subkategori sesuai jenis usaha. Jika subkategori tidak ada, tambah dengan klik tombol +'),

                TextInput::make('nama')
                    ->label('Nama Usaha')
                    ->columnSpanFull()
                    ->required()
                    ->hint('Nama usaha sesuai dengan dokumen resmi jika ada')
                    ->placeholder('Contoh: Toko Sumber Makmur'),

                TextInput::make('nama_pemilik')
                    ->label('Nama Pemilik')
                    ->required()
                    ->hint('Nama pemilik usaha')
                    ->placeholder('Contoh: Budi Santoso'),

                TextInput::make('nomor_pemilik')
                    ->label('Nomor Pemilik')
                    ->required()
                    ->tel()
                    ->hint('Nomor HP yang dapat dihubungi')
                    ->placeholder('Contoh: 081234567890')
                    ->rules(['regex:/^[0-9+\-\s]+$/']),

                Select::make('rw_id')
                    ->label('RW')
                    ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
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
                    ->hintIcon('heroicon-o-map')
                    ->hintIconTooltip('Pilih RW tempat usaha berada. RW mempengaruhi daftar RT yang tersedia'),

                Select::make('rt_id')
                    ->label('RT')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->relationship('rt', 'nomor', modifyQueryUsing: function (Builder $query, $get) {
                        $user = auth()->user();
                        $query->where('rw_id', $get('rw_id'));

                        if ($user->isRT()) {
                            $query->where('id', $user->rt_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(RT $rt) => "RT {$rt->nomor}")
                    ->hintIconTooltip('Pilih RT sesuai RW yang dipilih')
                    ->hintIcon('heroicon-o-map'),

                Textarea::make('alamat')
                    ->columnSpanFull()
                    ->required()
                    ->hint('Alamat lengkap usaha'),
            ]);
    }
}
