<?php

namespace App\Livewire\Pages;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\Shdk;
use App\Enums\StatusPerkawinan;
use App\Livewire\BaseLayout;
use App\Models\KategoriPendidikan;
use App\Models\Keluarga;
use App\Models\Pekerjaan;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Infografis extends BaseLayout
{
    protected string $pageTitle = 'Infografis';

    public function render()
    {
        // 1x query: ambil kolom yang diperlukan (tambahkan kolom lain bila perlu)
        $penduduks = Penduduk::select([
            'id',
            'keluarga_id',
            'jenis_kelamin',
            'tanggal_lahir',
            'pekerjaan_id',
            'pendidikan_id',
            'agama',
            'status_perkawinan',
            'shdk',
        ])->get();

        // Hitung umur untuk tiap penduduk (simpan di koleksi sebagai atribut 'age')
        $penduduks->each(function ($p) {
            try {
                $p->age = $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->age : null;
            } catch (\Exception $e) {
                $p->age = null;
            }
        });

        // jumlah kepala keluarga -> hitung distinct keluarga_id dimana shdk == 'Kepala Keluarga'
        $jumlah_kepala_keluarga = $penduduks
            ->where('shdk', Shdk::Kepala->value)
            ->pluck('keluarga_id')
            ->unique()
            ->count();

        // ======= Default kategori yang harus tampil walau 0 =======
        $ageGroups = [
            '0 s/d 4'   => [0, 4],
            '5 s/d 9'   => [5, 9],
            '10 s/d 14' => [10, 14],
            '15 s/d 19' => [15, 19],
            '20 s/d 24' => [20, 24],
            '25 s/d 29' => [25, 29],
            '30 s/d 34' => [30, 34],
            '35 s/d 39' => [35, 39],
            '40 s/d 44' => [40, 44],
            '45 s/d 49' => [45, 49],
            '50 s/d 54' => [50, 54],
            '55 s/d 59' => [55, 59],
            '60 s/d 64' => [60, 64],
            '65 keatas' => [65, 200],
        ];

        // ===========================
        // AGGREGASI DARI KOLEKSI (single-pass-ish)
        // ===========================
        // total L/P
        $total_L = $penduduks->where('jenis_kelamin', 'L')->count();
        $total_P = $penduduks->where('jenis_kelamin', 'P')->count();

        // kelompok umur: hitung berdasarkan $ageGroups (pastikan penduduk tanpa age diabaikan)
        $penduduk_umur = [];
        foreach ($ageGroups as $label => [$min, $max]) {
            $L = $penduduks->filter(function ($p) use ($min, $max) {
                return $p->age !== null && $p->age >= $min && $p->age <= $max && $p->jenis_kelamin === 'L';
            })->count();

            $P = $penduduks->filter(function ($p) use ($min, $max) {
                return $p->age !== null && $p->age >= $min && $p->age <= $max && $p->jenis_kelamin === 'P';
            })->count();

            $penduduk_umur[] = [
                'kelompok' => $label,
                'L' => $L,
                'P' => $P,
            ];
        }

        // === Convert penduduk_umur into easier arrays ===
        $umur_laki = [];
        $umur_perempuan = [];

        foreach ($penduduk_umur as $row) {
            $umur_laki[$row['kelompok']] = $row['L'];
            $umur_perempuan[$row['kelompok']] = $row['P'];
        }

        // ==========================
        // SUMMARY LAKI-LAKI
        // ==========================
        $max_laki_group = collect($umur_laki)->sortDesc()->keys()->first();
        $min_laki_group = collect($umur_laki)->sort()->keys()->first();

        $max_laki_value = $umur_laki[$max_laki_group];
        $min_laki_value = $umur_laki[$min_laki_group];

        $max_laki_percent = $total_L > 0 ? round(($max_laki_value / $total_L) * 100, 2) : 0;
        $min_laki_percent = $total_L > 0 ? round(($min_laki_value / $total_L) * 100, 2) : 0;

        $laki_summary = [
            'max_group' => $max_laki_group,
            'max_value' => $max_laki_value,
            'max_percent' => $max_laki_percent,
            'min_group' => $min_laki_group,
            'min_value' => $min_laki_value,
            'min_percent' => $min_laki_percent,
        ];

        // ==========================
        // SUMMARY PEREMPUAN
        // ==========================
        $max_p_group = collect($umur_perempuan)->sortDesc()->keys()->first();
        $min_p_group = collect($umur_perempuan)->sort()->keys()->first();

        $max_p_value = $umur_perempuan[$max_p_group];
        $min_p_value = $umur_perempuan[$min_p_group];

        $max_p_percent = $total_P > 0 ? round(($max_p_value / $total_P) * 100, 2) : 0;
        $min_p_percent = $total_P > 0 ? round(($min_p_value / $total_P) * 100, 2) : 0;

        $perempuan_summary = [
            'max_group' => $max_p_group,
            'max_value' => $max_p_value,
            'max_percent' => $max_p_percent,
            'min_group' => $min_p_group,
            'min_value' => $min_p_value,
            'min_percent' => $min_p_percent,
        ];

        $penduduk_dusun = Penduduk::with(['rt.rw'])
            ->select('rt_id', DB::raw('count(*) as total'))
            ->groupBy('rt_id')
            ->get()
            ->map(function ($row) {
                $rt = $row->rt;
                $rw = $rt ? $rt->rw : null;

                return [
                    'label' => 'RT.' . str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) .
                        '/RW.' . str_pad($rw->nomor ?? 0, 3, '0', STR_PAD_LEFT),
                    'total' => $row->total,
                ];
            });

        // Ambil label dari model kategori pendidikan
        $pendidikanLabels = KategoriPendidikan::pluck('name')->toArray();

        // Hitung jumlah setiap pendidikan sesuai data di kategori pendidikan
        $pendidikanCounts = KategoriPendidikan::withCount('penduduks')->pluck('penduduks_count', 'name')->toArray();

        // penduduk dengan pendidikan_id null masukkan ke kategori belum diisi
        $pendidikanCounts['Belum Diisi'] = Penduduk::whereNull('pendidikan_id')->count();

        // pekerjaan berdasarkan model
        // Ambil label enum
        $pekerjaanLabels = Pekerjaan::pluck('name', 'id')->toArray();

        // Hitung jumlah tiap pekerjaan
        $pekerjaanCounts = [];
        foreach ($pekerjaanLabels as $id => $name) {
            $count = Penduduk::where('pekerjaan_id', $id)->count();
            $pekerjaanCounts[$name] = $count;
        }

        // Urutkan dari jumlah terbanyak → sedikit
        arsort($pekerjaanCounts);

        // Ambil 6 pekerjaan teratas untuk kartu ringkasan
        $pekerjaanTop6 = array_slice($pekerjaanCounts, 0, 6, true);

        // Wajib Ikut Pilihan
        $currentYear = now()->year;
        $years = range($currentYear - 4, $currentYear); // 5 tahun terakhir

        $wajibPilihPerTahun = [];

        foreach ($years as $year) {
            $cutoff = Carbon::create($year, 1, 1)->subYears(17);

            $wajibPilihPerTahun[$year] = Penduduk::where('tanggal_lahir', '<=', $cutoff)->count();
        }

        // === Perkawinan ===
        $perkawinan = [];
        foreach (StatusPerkawinan::cases() as $case) {
            $perkawinan[] = [
                'title' => $case->value,
                'value' => $penduduks->where('status_perkawinan', $case->value)->count(),
                'icon'  => asset('images/marital/' . str($case->value)->slug('-') . '-icon.svg'),
            ];
        }

        $dataAgama = collect(Agama::cases())->map(function ($agama) {
            return [
                'title' => $agama->value,
                'value' => Penduduk::where('agama', $agama->value)->count(),
                'icon' => asset('images/religion/' . strtolower(str_replace(' ', '-', $agama->name)) . '-icon.png'),
            ];
        });

        return $this->layoutWithData(
            view('livewire.pages.infografis', [
                'jumlah_penduduk' => $penduduks->count(),
                'jumlah_kepala_keluarga' => $jumlah_kepala_keluarga,
                'jumlah_penduduk_laki' => $total_L,
                'jumlah_penduduk_perempuan' => $total_P,
                'penduduk_umur' => $penduduk_umur,
                'laki_summary' => $laki_summary,
                'perempuan_summary' => $perempuan_summary,
                'penduduk_dusun' => $penduduk_dusun,
                'pendidikan_labels' => $pendidikanLabels,
                'pendidikan_counts' => $pendidikanCounts,
                'pekerjaan_counts' => $pekerjaanCounts,
                'pekerjaan_top6'   => $pekerjaanTop6,
                'wajib_pilih' => $wajibPilihPerTahun,
                'perkawinan' => $perkawinan,
                'dataAgama' => $dataAgama,
            ])
        );
    }
}
