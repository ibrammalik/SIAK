<?php

namespace App\Http\Controllers;

use App\Enums\JenisKelamin;
use App\Enums\KategoriFasilitas;
use App\Enums\KategoriUsaha;
use App\Enums\Shdk;
use App\Enums\SubkategoriFasilitas;
use App\Enums\SubkategoriUsaha;
use App\Models\Keluarga;
use App\Models\Pekerjaan;
use App\Models\Penduduk;
use App\Models\RT;
use App\Models\RW;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LaporanMonografi extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->query('tipe');
        $wilayah = $request->query('wilayah');
        $id = $request->query('id');

        if ($wilayah === 'rw' && RW::where('id', $id)->doesntExist()) abort(404);
        if ($wilayah === 'rt' && RT::where('id', $id)->doesntExist()) abort(404);

        $judul = '';
        $data = $data = self::getData($wilayah, $id);

        if ($wilayah === 'rw') {
            $rw = RW::select('nomor')
                ->where('id', $id)
                ->first();
            $judul = "LAPORAN MONOGRAFI RW {$rw->nomor} KELURAHAN KALICARI";
        } elseif ($wilayah === 'rt') {
            $rt = RT::find($id);
            $judul = "LAPORAN MONOGRAFI RT {$rt->nomor} / RW {$rt->rw->nomor} KELURAHAN KALICARI";
        } else {
            $judul = "LAPORAN MONOGRAFI KELURAHAN KALICARI";
        }

        if ($tipe === 'excel') {
            // Render HTML view
            $html = view('exports.tesmonografi', [
                'data' => $data,
                'bulan' => now()->translatedFormat('F Y'),
                'judul' => $judul
            ])->render();

            // Return as Excel-readable HTML
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="laporan_monografi.xls"');
        } else {
            $html = view('exports.tesmonografi', [
                'judul' => $judul,
                'bulan' => now()->translatedFormat('F Y'),
                'data' => $data,
            ])->render();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('A4', 'potrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('dpi', 120);;

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf');
        }
    }

    public static function getData($type, $id)
    {
        $penduduks = Penduduk::select([
            'id',
            'keluarga_id',
            'jenis_kelamin',
            'tanggal_lahir',
            'pekerjaan_id',
            'pendidikan',
            'agama',
            'status_perkawinan',
            'shdk',
        ])
            ->when($type === 'rw' && $id, function (Builder $query) use ($id) {
                $query->where('rw_id', $id);
            })
            ->when($type === 'rt' && $id, function (Builder $query) use ($id) {
                $query->where('rt_id', $id);
            })
            ->get();

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

        // default pekerjaan (tambahkan sesuai kebutuhan)
        $defaultPekerjaan = Pekerjaan::pluck('name', 'id')->toArray();

        $defaultPekerjaan[null] = 'Belum diisi';

        // pekerjaan dihitung jika umur >= 10
        $pendudukPekerjaan = $penduduks->filter(function ($p) {
            return $p->age !== null && $p->age >= 10;
        });

        // group berdasarkan pekerjaannya
        $pekerjaanGrouped = $pendudukPekerjaan
            ->groupBy(function ($p) {
                return $p->pekerjaan_id;
            })
            ->map->count()
            ->toArray();

        // susun format final
        $pekerjaanFinal = [];
        foreach ($defaultPekerjaan as $id => $name) {
            $pekerjaanFinal[] = [
                'jenis' => $name,
                'jumlah' => $pekerjaanGrouped[$id] ?? 0,
            ];
        }

        // default pendidikan (sesuaikan dengan kebutuhan; akan ditambah jika ada lainnya)
        $defaultPendidikan = [
            'Perguruan Tinggi',
            'Tamat Akademi',
            'Tamat SLTA',
            'Tamat SLTP',
            'Tamat SD',
            'Tidak Tamat SD',
            'Belum Tamat SD',
            'Tidak Sekolah',
        ];

        // pendidikan: sama pola
        // pendidikan dihitung jika umur >= 5
        $pendudukPendidikan = $penduduks->filter(function ($p) {
            return $p->age !== null && $p->age >= 5;
        });

        // group berdasarkan pendidikan
        $pendidikanGrouped = $pendudukPendidikan
            ->groupBy(function ($p) {
                return trim($p->pendidikan ?? 'Tidak Diketahui');
            })
            ->map->count()
            ->toArray();

        // susun format final
        $pendidikanFinal = [];
        foreach ($defaultPendidikan as $jenjang) {
            $pendidikanFinal[] = [
                'jenjang' => $jenjang,
                'jumlah' => $pendidikanGrouped[$jenjang] ?? 0,
            ];
        }

        // tambahkan hasil lain dari DB yang tidak ada di default
        foreach ($pendidikanGrouped as $jenjang => $jumlah) {
            if (!in_array($jenjang, $defaultPendidikan)) {
                $pendidikanFinal[] = [
                    'jenjang' => $jenjang,
                    'jumlah' => $jumlah,
                ];
            }
        }

        // default agama
        $defaultAgama = [
            'Islam',
            'Kristen',
            'Katolik',
            'Hindu',
            'Buddha',
            'Konghucu',
            'Lainnya'
        ];

        // agama: sama pola
        $agamaGrouped = $penduduks->groupBy(function ($p) {
            return trim($p->agama ?? 'Lainnya');
        })->map->count()->toArray();

        $agamaFinal = [];
        foreach ($defaultAgama as $agamaName) {
            $agamaFinal[] = [
                'agama' => $agamaName,
                'jumlah' => $agamaGrouped[$agamaName] ?? 0,
            ];
        }
        foreach ($agamaGrouped as $nama => $jumlah) {
            if (!in_array($nama, $defaultAgama)) {
                $agamaFinal[] = [
                    'agama' => $nama,
                    'jumlah' => $jumlah,
                ];
            }
        }

        // default status perkawinan
        $defaultNikah = [
            'Belum Kawin',
            'Kawin',
            'Cerai Hidup',
            'Cerai Mati'
        ];

        // status perkawinan: sama
        $nikahGrouped = $penduduks->groupBy(function ($p) {
            return trim($p->status_perkawinan ?? 'Tidak Diketahui');
        })->map->count()->toArray();

        $nikahFinal = [];
        foreach ($defaultNikah as $status) {
            $nikahFinal[] = [
                'status' => $status,
                'jumlah' => $nikahGrouped[$status] ?? 0,
            ];
        }
        foreach ($nikahGrouped as $nama => $jumlah) {
            if (!in_array($nama, $defaultNikah)) {
                $nikahFinal[] = [
                    'status' => $nama,
                    'jumlah' => $jumlah,
                ];
            }
        }

        // ===========================
        // DATA USAHA (lengkap: semua kategori + subkategori, termasuk 0)
        // ===========================

        // siapkan struktur berdasarkan enum KategoriUsaha + SubkategoriUsaha::byKategori
        $usahaByKategori = [];
        foreach (KategoriUsaha::cases() as $kategoriEnum) {
            $key = $kategoriEnum->value; // disimpan di DB
            $usahaByKategori[$key] = [
                'label' => $kategoriEnum->getLabel(),
                'total' => 0,
                'sub' => [],
            ];

            // byKategori bisa menerima enum instance atau string tergantung implementasi.
            // Jika byKategori menerima string, ganti $kategoriEnum menjadi $kategoriEnum->value.
            $subcases = SubkategoriUsaha::byKategori($kategoriEnum);
            foreach ($subcases as $subcase) {
                $usahaByKategori[$key]['sub'][$subcase->value] = [
                    'label' => $subcase->getLabel(),
                    'total' => 0,
                ];
            }
        }

        // ambil data usaha dari DB (filter RW/RT jika perlu)
        $usahas = \App\Models\Usaha::select(['kategori', 'subkategori'])
            ->when($type === 'rw' && $id, fn($q) => $q->where('rw_id', $id))
            ->when($type === 'rt' && $id, fn($q) => $q->where('rt_id', $id))
            ->get();

        // masukkan data DB ke struktur (juga accept kategori/subkategori yang tidak ada di enum)
        foreach ($usahas as $u) {
            $cat = $u->kategori;
            $sub = $u->subkategori;

            $usahaByKategori[$cat->value]['total']++;

            $usahaByKategori[$cat->value]['sub'][$sub->value]['total']++;
        }

        // ===========================
        // DATA FASILITAS (lengkap: semua kategori + subkategori, termasuk 0)
        // ===========================

        // siapkan struktur berdasarkan enum KategoriFasilitas + SubkategoriFasilitas::byKategori
        $fasilitasByKategori = [];
        foreach (KategoriFasilitas::cases() as $kategoriEnum) {
            $key = $kategoriEnum->value;
            $fasilitasByKategori[$key] = [
                'label' => $kategoriEnum->getLabel(),
                'total' => 0,
                'sub' => [],
            ];

            // panggil byKategori dengan enum instance (atau ->value jika signature byKategori membutuhkan string)
            $subcases = SubkategoriFasilitas::byKategori($kategoriEnum);
            foreach ($subcases as $subcase) {
                $fasilitasByKategori[$key]['sub'][$subcase->value] = [
                    'label' => $subcase->getLabel(),
                    'total' => 0,
                ];
            }
        }

        // ambil data fasilitas dari DB (filter RW/RT jika perlu)
        $fasilitass = \App\Models\Fasilitas::select(['kategori', 'subkategori'])
            ->when($type === 'rw' && $id, fn($q) => $q->where('rw_id', $id))
            ->when($type === 'rt' && $id, fn($q) => $q->where('rt_id', $id))
            ->get();

        // masukkan data DB ke struktur (juga accept kategori/subkategori yang tidak ada di enum)
        foreach ($fasilitass as $f) {
            $cat = $f->kategori;
            $sub = $f->subkategori;

            $fasilitasByKategori[$cat->value]['total']++;

            $fasilitasByKategori[$cat->value]['sub'][$sub->value]['total']++;
        }

        // Susun $data final (sama struktur dengan contoh static-mu)
        $data = [
            'jumlah_kepala_keluarga' => $jumlah_kepala_keluarga,
            'total_L' => $total_L,
            'total_P' => $total_P,
            'total_umur' => $penduduks->count(),
            'total_pekerjaan' => $pendudukPekerjaan->count(),
            'total_pendidikan' => $pendudukPendidikan->count(),
            'total_agama' => $penduduks->count(),
            'total_perkawinan' => $penduduks->count(),
            'penduduk_umur' => $penduduk_umur,
            'pekerjaan' => $pekerjaanFinal,
            'pendidikan' => $pendidikanFinal,
            'agama' => $agamaFinal,
            'nikah' => $nikahFinal,
            'usaha' => $usahaByKategori,
            'total_usaha' => $usahas->count(),
            'fasilitas' => $fasilitasByKategori,
            'total_fasilitas' => $fasilitass->count(),
            // 'wni_keturunan' => $wni_keturunan,
        ];

        return $data;
    }

    public static function test()
    {
        // format data

        // $data = [
        //     'jumlah_kepala_keluarga' => Keluarga::count(),
        //     'total_L' => Penduduk::where('jenis_kelamin', JenisKelamin::L->value)->count(),
        //     'total_P' => Penduduk::where('jenis_kelamin', JenisKelamin::P->value)->count(),
        //     'total_umur' => Penduduk::count(),
        //     'total_pekerjaan' => 500,
        //     'total_pendidikan' => 500,
        //     'total_agama' => Penduduk::count(),
        //     'total_perkawinan' => Penduduk::count(),
        //     'penduduk_umur' => [
        //         ['kelompok' => '0 s/d 4', 'L' => 432, 'P' => 409],
        //         ['kelompok' => '5 s/d 9', 'L' => 474, 'P' => 509],
        //         ['kelompok' => '10 s/d 14', 'L' => 496, 'P' => 482],
        //         ['kelompok' => '15 s/d 19', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '20 s/d 24', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '25 s/d 29', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '30 s/d 34', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '35 s/d 39', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '40 s/d 44', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '45 s/d 49', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '50 s/d 54', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '55 s/d 59', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '60 s/d 65', 'L' => 474, 'P' => 501],
        //         ['kelompok' => '65 keatas', 'L' => 474, 'P' => 501],
        //     ],
        //     'pekerjaan' => [
        //         ['jenis' => 'Petani Sendiri', 'jumlah' => 190],
        //         ['jenis' => 'Buruh Tani', 'jumlah' => 560],
        //         ['jenis' => 'Nelayan', 'jumlah' => 155],
        //         ['jenis' => 'Pengusaha', 'jumlah' => 830],
        //         ['jenis' => 'Buruh Industri', 'jumlah' => 735],
        //         ['jenis' => 'Buruh Bangunan', 'jumlah' => 735],
        //         ['jenis' => 'Dagang', 'jumlah' => 735],
        //         ['jenis' => 'Pengangkutan', 'jumlah' => 735],
        //         ['jenis' => 'ASN', 'jumlah' => 735],
        //         ['jenis' => 'Polri', 'jumlah' => 735],
        //         ['jenis' => 'TNI', 'jumlah' => 735],
        //         ['jenis' => 'Pensiunan', 'jumlah' => 735],
        //         ['jenis' => 'Lain-lain', 'jumlah' => 735],
        //     ],
        //     'pendidikan' => [
        //         ['jenjang' => 'Perguruan Tinggi', 'jumlah' => 1022],
        //         ['jenjang' => 'Tamat Akademi', 'jumlah' => 551],
        //         ['jenjang' => 'Tamat SLTA', 'jumlah' => 1531],
        //         ['jenjang' => 'Tamat SLTP', 'jumlah' => 1110],
        //         ['jenjang' => 'Tamat SD', 'jumlah' => 752],
        //         ['jenjang' => 'Tidak Tamat SD', 'jumlah' => 531],
        //         ['jenjang' => 'Belum Tamat SD', 'jumlah' => 531],
        //         ['jenjang' => 'Tidak Sekolah', 'jumlah' => 154],
        //     ],
        //     'agama' => [
        //         ['agama' => 'Islam', 'jumlah' => 7985],
        //         ['agama' => 'Kriten', 'jumlah' => 395],
        //         ['agama' => 'Katolik', 'jumlah' => 191],
        //         ['agama' => 'Hindu', 'jumlah' => 10],
        //         ['agama' => 'Buddha', 'jumlah' => 5],
        //         ['agama' => 'Konghucu', 'jumlah' => 5],
        //         ['agama' => 'Lainnya', 'jumlah' => 5],
        //     ],
        //     'wni_keturunan' => [
        //         ['keturunan' => 'Cina RRC', 'L' => 0, 'P' => 0],
        //         ['keturunan' => 'Belanda', 'L' => 0, 'P' => 0],
        //         ['keturunan' => 'Arab', 'L' => 0, 'P' => 0],
        //         ['keturunan' => 'India', 'L' => 0, 'P' => 0],
        //     ],
        //     'nikah' => [
        //         ['status' => 'Belum Kawin', 'jumlah' => 7985],
        //         ['status' => 'Kawin', 'jumlah' => 7985],
        //         ['status' => 'Cerai Hidup', 'jumlah' => 7985],
        //         ['status' => 'Cerai Mati', 'jumlah' => 7985],
        //     ],
        // ];


        // wni_keturunan: jika kamu tidak punya data, tetap tampilkan default dengan 0
        // $wni_keturunan = [
        //     ['keturunan' => 'Cina RRC', 'L' => 0, 'P' => 0],
        //     ['keturunan' => 'Belanda', 'L' => 0, 'P' => 0],
        //     ['keturunan' => 'Arab', 'L' => 0, 'P' => 0],
        //     ['keturunan' => 'India', 'L' => 0, 'P' => 0],
        // ];

        // return self::getData();
    }
}
