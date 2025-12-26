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
                ->rules(['date'])
                ->castStateUsing(function ($state) {
                    $state = Carbon::createFromFormat('d/m/Y', $state)->format('Y-m-d');
                    return $state;
                }),

            ImportColumn::make('jenis_kelamin')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('agama')
                ->rules(['max:255']),

            ImportColumn::make('pendidikan')
                ->rules(['max:255'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('status_perkawinan')
                ->rules(['max:255']),

            ImportColumn::make('pekerjaan')
                ->rules(['max:255'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('status_kependudukan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('shdk')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Penduduk
    {
        $row = $this->data;

        // validasi enum
        $this->validateEnum($row['jenis_kelamin'], JenisKelamin::class, 'Jenis Kelamin');
        $this->validateEnum($row['agama'] ?? null, Agama::class, 'Agama');
        $this->validateEnum($row['status_perkawinan'] ?? null, StatusPerkawinan::class, 'Status Perkawinan');
        $this->validateEnum($row['status_kependudukan'] ?? null, StatusKependudukan::class, 'Status Kependudukan');
        $this->validateEnum($row['shdk'] ?? null, Shdk::class, 'SHDK');

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
    private function validateEnum(string|null $value, string $enumClass, string $label): void
    {
        if ($value === null || $value === '') {
            return; // field nullable => skip
        }

        if ($enumClass::tryFrom($value) === null) {
            $allowed = implode(', ', $enumClass::values());
            throw new RowImportFailedException("$label tidak valid: [$value]. Harus salah satu dari: $allowed");
        }
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
