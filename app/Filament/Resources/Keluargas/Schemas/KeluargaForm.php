<?php

namespace App\Filament\Resources\Keluargas\Schemas;

use App\Filament\Resources\RWS\Schemas\RWForm;
use App\Models\RT;
use App\Models\RW;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class KeluargaForm
{
    public static function getFormSchema()
    {
        return [
            Select::make('rw_id')
                ->label('RW')
                ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                    $user = Auth::user();
                    if ($user->isRW() || $user->isRT()) {
                        $query->where('id', $user->rw_id);
                    }
                })
                ->afterStateUpdated(fn(callable $set) => $set('rt_id', null))
                ->live()
                ->preload()
                ->searchable()
                ->required()
                ->afterStateHydrated(function ($set) {
                    $user = Auth::user();
                    if ($user->isRW() || $user->isRT()) $set('rw_id', $user->rw_id);
                })
                ->createOptionAction(
                    fn($action) => $action->visible(
                        Auth::user()->isSuperAdmin() || Auth::user()->isKelurahan()
                    )
                )
                ->createOptionForm([
                    // === Nomor RW ===
                    TextInput::make('nomor')
                        ->label('Nomor RW')
                        ->placeholder('Contoh: 1')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->numeric()
                        ->hintIcon('heroicon-m-numbered-list')
                        ->hintIconTooltip('Isi dengan angka urut RW di wilayah Anda, misalnya 1, 2, atau 3.')
                        ->columnSpanFull(),
                ])
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
                ->afterStateHydrated(function ($set) {
                    $user = Auth::user();
                    if ($user->isRT()) $set('rt_id', $user->rt_id);
                })
                ->createOptionForm([
                    // === PILIH RW ===
                    Select::make('rw_id')
                        ->label('Pilih RW')
                        ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                            $user = Auth::user();
                            if ($user->isRW()) {
                                $query->where('id', $user->rw_id);
                            }
                        })
                        ->preload()
                        ->searchable()
                        ->required()
                        ->afterStateHydrated(function ($set) {
                            $user = Auth::user();
                            if ($user->isRW() || $user->isRT()) $set('rw_id', $user->rw_id);
                        })
                        ->hintIcon('heroicon-m-map')
                        ->hintIconTooltip('Pilih RW tempat RT ini berada.')
                        ->helperText('RT harus berada di bawah satu RW tertentu.')
                        ->columnSpanFull()
                        ->live(),

                    TextInput::make('nomor')
                        ->label('Nomor RT')
                        ->placeholder('Contoh: 1')
                        ->required()
                        ->numeric()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: function (Unique $rule, $get) {
                                return $rule->where('rw_id', $get('rw_id'));
                            }
                        )
                        ->hintIcon('heroicon-m-numbered-list')
                        ->hintIconTooltip('Isi dengan angka urut RT di dalam RW yang dipilih.')
                        ->columnSpanFull(),
                ])
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
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getFormSchema());
    }
}
