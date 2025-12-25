<?php

namespace App\Filament\Resources\Fasilitas\Schemas;

use App\Models\KategoriFasilitas;
use App\Models\RT;
use App\Models\RW;
use App\Models\SubkategoriFasilitas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FasilitasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kategori_fasilitas_id')
                    ->label('Kategori')
                    ->relationship('kategoriFasilitas', 'name')
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('subkategori_fasilitas_id', null);
                    })
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required(),
                    ])
                    ->required()
                    ->hintIcon('heroicon-o-information-circle')
                    ->hintIconTooltip('Pilih kategori utama fasilitas'),

                Select::make('subkategori_fasilitas_id')
                    ->label('Subkategori')
                    ->relationship(
                        name: 'subkategoriFasilitas',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, $get) {
                            $kategoriFasilitasId = $get('kategori_fasilitas_id');
                            if ($kategoriFasilitasId !== null) {
                                return $query->where('kategori_fasilitas_id', $kategoriFasilitasId);
                            }
                        }
                    )
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $sub = SubkategoriFasilitas::find($state);
                            if ($sub) {
                                $set('kategori_fasilitas_id', $sub->kategori_fasilitas_id);
                            }
                        }
                    })
                    ->createOptionForm([
                        Select::make('kategori_fasilitas_id')
                            ->label('Kategori')
                            ->options(KategoriFasilitas::pluck('name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Subkategori')
                            ->required(),
                    ])
                    ->required()
                    ->hintIcon('heroicon-o-information-circle')
                    ->hintIconTooltip('Pilih subkategori sesuai jenis fasilitas. Jika subkategori tidak ada, tambah dengan klik tombol +'),

                TextInput::make('nama')
                    ->label('Nama Fasilitas')
                    ->columnSpanFull()
                    ->required()
                    ->hint('Nama fasilitas sesuai dengan dokumen resmi jika ada')
                    ->helperText('Contoh: SD Negeri 02 Kalicari'),

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
                    ->hintIcon('heroicon-o-map')
                    ->hintIconTooltip('Pilih RW tempat fasilitas berada. RW mempengaruhi daftar RT yang tersedia'),

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
                    ->hintIconTooltip('Pilih RT sesuai RW yang dipilih')
                    ->hintIcon('heroicon-o-map'),

                Textarea::make('alamat')
                    ->columnSpanFull()
                    ->required()
                    ->hint('Alamat lengkap fasilitas')
            ]);
    }
}
