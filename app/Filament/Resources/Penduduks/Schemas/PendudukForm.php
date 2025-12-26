<?php

namespace App\Filament\Resources\Penduduks\Schemas;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\RT;
use App\Models\RW;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\StatusPerkawinan;
use App\Enums\StatusKependudukan;
use App\Enums\Shdk;
use App\Filament\Resources\Keluargas\Schemas\KeluargaForm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PendudukForm
{
    public static function getFormSchema()
    {
        return [

            // === 🏠 DATA KELUARGA ===
            Fieldset::make('Data Keluarga')
                ->schema([
                    Select::make('keluarga_id')
                        ->label('Nomor KK')
                        ->relationship(
                            name: 'keluarga',
                            titleAttribute: 'no_kk',
                            modifyQueryUsing: function (Builder $query) {
                                $user = Auth::user();
                                if ($user->isRW()) return $query->where('rw_id', $user->rw_id);
                                if ($user->isRT()) return $query->where('rt_id', $user->rt_id);
                            }
                        )
                        ->preload()
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $keluarga = Keluarga::with(['rw', 'rt'])->find($state);

                            if ($keluarga) {
                                $set('rw_id', $keluarga->rw->id ?? null);
                                $set('rt_id', $keluarga->rt->id ?? null);
                                $set('alamat', $keluarga->alamat ?? null);
                            } else {
                                $set('rw_id', null);
                                $set('rt_id', null);
                                $set('alamat', null);
                            }
                        })
                        ->createOptionForm(KeluargaForm::getFormSchema())
                        ->hintIcon('heroicon-m-identification')
                        ->hintIconTooltip('Pilih nomor Kartu Keluarga (KK) yang sudah terdaftar.')
                        ->helperText('Jika KK belum ada, buat data keluarga terlebih dahulu dengan klik tombol + di kanan.')
                        ->columnSpanFull(),

                    Fieldset::make('Wilayah RT / RW')
                        ->schema([
                            Select::make('rt_id')
                                ->label('RT')
                                ->options(RT::query()->pluck('nomor', 'id'))
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->hintIcon('heroicon-m-map-pin')
                                ->hintIconTooltip('RT akan terisi otomatis berdasarkan KK yang dipilih.')
                                ->helperText('Nilai RT akan mengikuti data keluarga.'),

                            Select::make('rw_id')
                                ->label('RW')
                                ->options(RW::query()->pluck('nomor', 'id'))
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->hintIcon('heroicon-m-map')
                                ->hintIconTooltip('RW akan terisi otomatis berdasarkan KK yang dipilih.')
                                ->helperText('Nilai RW akan mengikuti data keluarga.'),

                            TextArea::make('alamat')
                                ->columnSpanFull()
                                ->disabled(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->visible(fn(Get $get) => $get('keluarga_id') !== null),
                ])
                ->columnSpanFull(),

            // === 👤 DATA PRIBADI ===
            Fieldset::make('Data Pribadi')
                ->schema([
                    TextInput::make('nik')
                        ->label('NIK')
                        ->required()
                        ->length(16)
                        ->maxLength(16)
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'pattern' => '[0-9]*',
                            'oninput' => "this.value = this.value.replace(/\\D/g,'').slice(0,16)",
                        ])
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull()
                        ->hintIcon('heroicon-m-identification')
                        ->hintIconTooltip('Nomor Induk Kependudukan terdiri dari 16 digit unik.'),

                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->hintIcon('heroicon-m-user-circle')
                        ->hintIconTooltip('Tuliskan nama lengkap sesuai dokumen resmi.'),

                    TextInput::make('no_telp')
                        ->label('Nomor Telepon')
                        ->required()
                        ->hintIcon('heroicon-m-phone')
                        ->hintIconTooltip('Masukkan nomor telepon aktif untuk keperluan kontak.'),

                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required()
                        ->hintIcon('heroicon-m-map-pin')
                        ->hintIconTooltip('Tuliskan kota atau kabupaten tempat lahir.'),

                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->hintIcon('heroicon-m-calendar')
                        ->hintIconTooltip('Pilih tanggal lahir sesuai dokumen resmi.'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            // === 📋 DATA KEPENDUDUKAN ===
            Fieldset::make('Data Kependudukan')
                ->schema([
                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(JenisKelamin::options())
                        ->native(false)
                        ->required()
                        ->hintIcon('heroicon-m-user')
                        ->hintIconTooltip('Pilih jenis kelamin sesuai dokumen kependudukan.'),

                    Select::make('agama')
                        ->label('Agama')
                        ->options(Agama::options())
                        ->native(false)
                        ->required()
                        ->hintIcon('heroicon-m-book-open')
                        ->hintIconTooltip('Pilih agama sesuai identitas resmi.'),

                    Select::make('pendidikan_id')
                        ->label('Pendidikan Terakhir')
                        ->relationship('pendidikan', 'name')
                        ->native(false)
                        ->required()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Kategori')
                                ->required()
                        ])
                        ->hintIcon('heroicon-m-academic-cap')
                        ->hintIconTooltip('Pilih pendidikan terakhir yang ditempuh.'),

                    Select::make('status_perkawinan')
                        ->label('Status Perkawinan')
                        ->options(StatusPerkawinan::options())
                        ->native(false)
                        ->required()
                        ->hintIcon('heroicon-m-heart')
                        ->hintIconTooltip('Pilih status perkawinan saat ini.'),

                    Select::make('pekerjaan_id')
                        ->label('Pekerjaan')
                        ->relationship('pekerjaan', 'name')
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Kategori')
                                ->required()
                        ])
                        ->hintIcon('heroicon-m-briefcase')
                        ->hintIconTooltip('Masukkan jenis pekerjaan atau profesi saat ini. Klik tombol + untuk menambah pekerjaan yang belum ada di kategori.'),

                    Select::make('status_kependudukan')
                        ->label('Status Kependudukan')
                        ->options(StatusKependudukan::options())
                        ->native(false)
                        ->required()
                        ->hintIcon('heroicon-m-home')
                        ->hintIconTooltip('Status menunjukkan apakah penduduk menetap atau sementara.'),

                    Select::make('shdk')
                        ->label('Status Dalam Keluarga')
                        ->options(Shdk::options())
                        ->native(false)
                        ->required()
                        ->rule(function (callable $get) {
                            if ($get('shdk') === 'Kepala') {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $keluargaId = $get('keluarga_id');
                                    $currentId = $get('id'); // ID record yang sedang diedit

                                    if ($keluargaId) {
                                        $query = Penduduk::where('keluarga_id', $keluargaId)
                                            ->where('shdk', 'Kepala');

                                        if ($currentId) {
                                            $query->where('id', '!=', $currentId);
                                        }

                                        if ($query->exists()) {
                                            $fail('Keluarga ini sudah memiliki Kepala.');
                                        }
                                    }
                                };
                            }
                            return null;
                        })
                        ->columnSpanFull()
                        ->hintIcon('heroicon-m-user-group')
                        ->hintIconTooltip('Menentukan posisi penduduk dalam keluarga, misalnya Kepala, Istri, atau Anak.')
                        ->helperText('Anda juga dapat menetapkan Kepala Keluarga dengan memilih status "Kepala".'),
                ])
                ->columns(3)
                ->columnSpanFull(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::getFormSchema());
    }
}
