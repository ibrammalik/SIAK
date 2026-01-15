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
                ->numeric()
                ->rules(['integer'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('rt')
                ->numeric()
                ->rules(['integer'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('no_kk')
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('alamat')
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('pekerjaan')
                ->rules(['max:255'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('pendidikan')
                ->rules(['max:255'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules(['unique:penduduks', 'required', 'max:255']),

            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('no_telp')
                ->rules(['max:255']),

            ImportColumn::make('tempat_lahir')
                ->rules(['max:255']),

            ImportColumn::make('tanggal_lahir')
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
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, JenisKelamin::class, 'Jenis Kelamin')
                ),

            ImportColumn::make('agama')
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, Agama::class, 'Agama', true)
                ),

            ImportColumn::make('status_perkawinan')
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, StatusPerkawinan::class, 'Status Perkawinan', true)
                ),

            ImportColumn::make('status_kependudukan')
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, StatusKependudukan::class, 'Status Kependudukan')
                ),

            ImportColumn::make('shdk')
                ->rules(['required', 'max:255'])
                ->castStateUsing(
                    fn($state) =>
                    self::castEnum($state, Shdk::class, 'SHDK')
                ),
        ];
    }

    public function resolveRecord(): ?Penduduk
    {
        $row = $this->data;

        // Validasi RW
        if (!is_numeric($row['rw'])) {
            throw new RowImportFailedException("RW harus angka. Ditemukan: {$row['rw']}");
        }

        $rw = RW::firstOrCreate(
            ['nomor' => $row['rw']],
        );

        // Validasi RT
        if (!is_numeric($row['rt'])) {
            throw new RowImportFailedException("RT harus angka. Ditemukan: {$row['rt']}");
        }

        $rt = RT::firstOrCreate([
            'nomor' => $row['rt'],
            'rw_id' => $rw->id
        ]);

        // Validasi role ketua rw / rt
        $user = Auth::user();

        if ($user->isRW() && $user->rw_id !== $rw->id) {
            throw new RowImportFailedException(
                "Import ditolak. RW pada data ({$row['rw']}) tidak sesuai dengan wilayah Anda RW ({$user->rw->nomor})."
            );
        }

        if ($user->isRT() && $user->rt_id !== $rt->id) {
            throw new RowImportFailedException(
                "Import ditolak. RT pada data ({$row['rt']}) tidak sesuai dengan wilayah Anda (RT {$user->rt->nomor} / RW {$user->rw->nomor})."
            );
        }

        // Validasi No KK
        if (!is_numeric($row['no_kk'])) {
            throw new RowImportFailedException("No KK harus angka. Ditemukan: {$row['no_kk']}");
        }

        $keluarga = Keluarga::firstOrCreate(
            ['no_kk' => $row['no_kk']],
            [
                'rw_id'   => $rw->id,
                'rt_id'   => $rt->id,
                'alamat'  => $row['alamat'] ?? '-',
            ]
        );

        // Pekerjaan, cari atau baru. Pros implementasi lebih gampang ketimbang
        // pakai pekerjaan yang sudah ada dan fail ketika pekerjaan penduduk yang
        // diimport tidak ada. Cons kalau input pekerjaan penulisan tidak konsisten
        // seperti (huruf kapital, typo dalam penulisan) maka data tidak konsisten.
        $pekerjaan = Pekerjaan::firstOrCreate(
            ['name' => $row['pekerjaan']],
            ['name' => $row['pekerjaan']],
        );

        // sama seperti pekerjaan
        $pendidikan = KategoriPendidikan::firstOrCreate(
            ['name' => $row['pendidikan']],
            ['name' => $row['pendidikan']],
        );

        // return record baru penduduk, kolom lain terisi otomatis.
        return new Penduduk([
            'rw_id'       => $rw->id,
            'rt_id'       => $rt->id,
            'keluarga_id' => $keluarga->id,
            'pekerjaan_id' => $pekerjaan->id,
            'pendidikan_id' => $pendidikan->id,
        ]);
    }

    // Fungsi helper untuk validasi enum
    private static function castEnum(mixed $state, string $enumClass, string $label, bool $nullable = false): ?string
    {
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
        $body = 'Your penduduk import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
