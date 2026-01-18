<?php

namespace App\Filament\Imports;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\Shdk;
use App\Enums\StatusKependudukan;
use App\Enums\StatusPerkawinan;
use App\Models\KategoriPendidikan;
use App\Models\Keluarga;
use App\Models\Pekerjaan;
use App\Models\Penduduk;
use App\Models\RT;
use App\Models\RW;
use Carbon\Carbon;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class PendudukImporter extends Importer
{
    protected static ?string $model = Penduduk::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('rw')
                ->requiredMapping()
                ->rules(['required', 'integer', 'digits_between:1,2', 'min:1', 'regex:/^[1-9][0-9]?$/'])
                ->fillRecordUsing(function ($state,  Penduduk $record) {
                    $rw = RW::firstOrCreate(['nomor' => $state]);
                    $record->rw_id = $rw->id;
                }),

            ImportColumn::make('rt')
                ->requiredMapping()
                ->rules(['required', 'integer', 'digits_between:1,2', 'min:1', 'regex:/^[1-9][0-9]?$/'])
                ->fillRecordUsing(function ($state, $data, Penduduk $record) {
                    // RW nomor comes from the same row import data
                    $rwNomor = $data['rw'];

                    // Resolve RW
                    $rw = RW::firstOrCreate(['nomor' => $rwNomor]);

                    // Resolve RT under RW
                    $rt = RT::firstOrCreate([
                        'nomor' => $state,
                        'rw_id' => $rw->id,
                    ]);

                    // Assign FK explicitly
                    $record->rt_id = $rt->id;
                    $record->rw_id = $rw->id;
                }),

            ImportColumn::make('no_kk')
                ->requiredMapping()
                ->rules([
                    'required',
                    'digits:16',
                    'regex:/^[0-9]{16}$/',
                ])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('alamat')
                ->requiredMapping()
                ->rules(['required'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('pekerjaan')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(function ($state, Penduduk $record) {
                    // Pekerjaan, cari atau baru. Pros implementasi lebih gampang ketimbang
                    // pakai pekerjaan yang sudah ada dan fail ketika pekerjaan penduduk yang
                    // diimport tidak ada. Cons kalau input pekerjaan penulisan tidak konsisten
                    // seperti (huruf kapital, typo dalam penulisan) maka data tidak konsisten.
                    $pekerjaan = Pekerjaan::firstOrCreate(['name' => trim($state)]);
                    $record->pekerjaan_id = $pekerjaan->id;
                }),

            ImportColumn::make('pendidikan')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(function ($state, Penduduk $record) {
                    $pendidikan = KategoriPendidikan::firstOrCreate(['name' => trim($state)]);
                    $record->pendidikan_id = $pendidikan->id;
                }),

            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules([
                    'required',
                    'unique:penduduks',
                    'digits:16',
                    'regex:/^[0-9]{16}$/',
                ]),

            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('no_telp')
                ->requiredMapping()
                ->rules([
                    'nullable',
                    'string',
                    'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/',
                ]),

            ImportColumn::make('tempat_lahir')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('tanggal_lahir')
                ->requiredMapping()
                ->rules(['date', 'required'])
                ->castStateUsing(function ($state) {
                    // Normalisasi: 3/7/1965 -> 03/07/1965
                    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $state, $m)) {
                        $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                        $year  = $m[3];

                        return Carbon::createFromFormat(
                            'd/m/Y',
                            "$day/$month/$year"
                        )->format('Y-m-d');
                    }

                    throw new RowImportFailedException(
                        "Format tanggal lahir tidak valid: [$state]. Gunakan DD/MM/YYYY"
                    );
                }),

            ImportColumn::make('jenis_kelamin')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, JenisKelamin::class, 'Jenis Kelamin')
                ),

            ImportColumn::make('agama')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, Agama::class, 'Agama', true)
                ),

            ImportColumn::make('status_perkawinan')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, StatusPerkawinan::class, 'Status Perkawinan', true)
                ),

            ImportColumn::make('status_kependudukan')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, StatusKependudukan::class, 'Status Kependudukan')
                ),

            ImportColumn::make('shdk')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, Shdk::class, 'SHDK')
                ),
        ];
    }

    public function resolveRecord(): ?Penduduk
    {
        // return Penduduk::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'nik' => $this->data['nik'],
        // ]);

        return new Penduduk();
    }

    protected function beforeFill(): void
    {
        // Validasi role ketua rw / rt
        $user = Auth::user();

        $importRw = $this->data['rw'];
        $importRt = $this->data['rt'];

        if ($user->isRW() && $user->rw?->nomor !== $importRw) {
            throw new RowImportFailedException(
                "Import ditolak. Data (RW {$importRw}) tidak sesuai dengan wilayah Anda (RW {$user->rw->nomor})."
            );
        }

        if ($user->isRT() && $user->rw?->nomor !== $importRw && $user->rt?->nomor !== $importRt) {
            throw new RowImportFailedException(
                "Import ditolak. Data (RT {$importRt}/ RW {$importRw}) tidak sesuai dengan wilayah Anda (RT {$user->rt->nomor} / RW {$user->rw->nomor})."
            );
        }
    }

    protected function afterCreate(): void
    {
        // Keluarga
        $noKk   = $this->data['no_kk'];
        $alamat = $this->data['alamat'];
        $shdk   = $this->data['shdk'];

        // RW / RT resolved earlier via ImportColumn::fillrecordusing()
        $rwId = $this->record->rw_id;
        $rtId = $this->record->rt_id;

        // Create or update keluarga (FULL ATTRIBUTES)
        $keluarga = Keluarga::firstOrCreate(
            ['no_kk' => $noKk],
            [
                'alamat' => $alamat,
                'rw_id'  => $rwId,
                'rt_id'  => $rtId,
            ]
        );

        // Assign keluarga_id to imported record
        $this->record->update(['keluarga_id' => $keluarga->id]);

        // === Kepala Keluarga Logic ===
        if ($shdk === Shdk::Kepala->value) {
            if ($keluarga->kepala_id !== null) {
                throw new RowImportFailedException("Sudah ada Kepala Keluarga pada KK {$noKk}");
            }

            $keluarga->update(['kepala_id' => $this->record->id]);
        }
    }

    // Fungsi helper untuk validasi enum
    private static function castEnum(mixed $state, string $enumClass, string $label, bool $nullable = false): ?string
    {
        $state = trim($state);

        if (blank($state)) {
            if ($nullable) {
                return null;
            }

            throw new RowImportFailedException("$label wajib diisi.");
        }

        $enum = $enumClass::fromInsensitive($state);

        if ($enum === null) {
            $allowed = implode(', ', $enumClass::values());

            throw new RowImportFailedException(
                "$label tidak valid: [$state]. Harus salah satu dari: $allowed"
            );
        }

        return $enum->value;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses impor data penduduk telah selesai dan ' . Number::format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
