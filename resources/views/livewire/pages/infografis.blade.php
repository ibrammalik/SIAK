<div id="infografis-page">
    <section class="max-w-7xl mx-auto px-6 pt-32">
        {{-- Judul halaman --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-green-800 mb-2">
                INFOGRAFIS KELURAHAN {{ strtoupper($nama_kelurahan) }}
            </h1>
            <p class="text-gray-600 text-lg">Data dan informasi statistik
                wilayah
                kelurahan secara interaktif dan visual.</p>
        </div>

        {{-- Navigasi kategori --}}
        <div class="flex justify-center gap-12 border-b border-gray-200 mb-10 text-center text-gray-600 font-medium">
            <a class="tab-active pb-3 flex flex-col items-center hover:text-green-700 transition">
                <i class="fa-solid fa-users text-2xl mb-1"></i>
                <span>Penduduk</span>
            </a>
        </div>

        {{-- Seksi Demografi --}}
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-3xl font-bold text-green-800 mb-4">DEMOGRAFI
                    PENDUDUK</h2>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Memberikan informasi lengkap mengenai karakteristik
                    demografi penduduk
                    suatu wilayah.
                    Mulai dari jumlah penduduk, usia, jenis kelamin, tingkat
                    pendidikan,
                    pekerjaan, agama, dan
                    aspek penting lainnya yang menggambarkan komposisi populasi
                    secara
                    rinci.
                </p>
                <a target="_blank" href="{{ route('preview.monografi') }}"
                    class="inline-block bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded-lg font-medium transition">
                    Lihat Monografi
                </a>
            </div>
            <div class="flex justify-center">
                <img src="/images/demografi.svg" alt="Grafik Demografi" class="max-w-md w-full">
            </div>
        </div>

        {{-- Contoh grafik / data --}}
        <div class="mt-12">
            <h3 class="text-2xl font-semibold text-green-700 mb-6">Jumlah
                Penduduk dan Kepala Keluarga</h3>

            {{-- Ringkasan Jumlah Penduduk --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                {{-- Total Penduduk --}}
                <div class="flex items-center bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                    <img src="{{ asset('images/penduduk-total.svg') }}" alt="Total Penduduk" class="w-14 h-14 mr-4">
                    <div>
                        <h4 class="text-gray-500 text-sm font-semibold uppercase">
                            Total
                            Penduduk</h4>
                        <p class="text-2xl font-bold text-green-700">
                            {{ $jumlah_penduduk }} <span class="text-base font-medium text-gray-600">Jiwa</span>
                        </p>
                    </div>
                </div>

                {{-- Kepala Keluarga --}}
                <div class="flex items-center bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                    <img src="{{ asset('images/kk.svg') }}" alt="Kepala Keluarga" class="w-14 h-14 mr-4">
                    <div>
                        <h4 class="text-gray-500 text-sm font-semibold uppercase">
                            Kepala
                            Keluarga</h4>
                        <p class="text-2xl font-bold text-green-700">
                            {{ $jumlah_kepala_keluarga }} <span class="text-base font-medium text-gray-600">Jiwa</span>
                        </p>
                    </div>
                </div>

                {{-- Perempuan --}}
                <div class="flex items-center bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                    <img src="{{ asset('images/perempuan.svg') }}" alt="Perempuan" class="w-14 h-14 mr-4">
                    <div>
                        <h4 class="text-gray-500 text-sm font-semibold uppercase">
                            Perempuan
                        </h4>
                        <p class="text-2xl font-bold text-green-700">
                            {{ $jumlah_penduduk_perempuan }} <span
                                class="text-base font-medium text-gray-600">Jiwa</span>
                        </p>
                    </div>
                </div>

                {{-- Laki-Laki --}}
                <div class="flex items-center bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                    <img src="{{ asset('images/laki.svg') }}" alt="Laki-laki" class="w-14 h-14 mr-4">
                    <div>
                        <h4 class="text-gray-500 text-sm font-semibold uppercase">
                            Laki-Laki
                        </h4>
                        <p class="text-2xl font-bold text-green-700">
                            {{ $jumlah_penduduk_laki }} <span class="text-base font-medium text-gray-600">Jiwa</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- SECTION: Berdasarkan Kelompok Umur --}}
        {{-- ========================================================= --}}
        <section id="umur" class="mt-12 bg-gray-50">
            <div>
                <h3 class="text-2xl font-bold text-green-800 mb-6">
                    Berdasarkan Kelompok Umur
                </h3>

                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <canvas id="umurChart" height="400"></canvas>
                </div>

                <div class="space-y-4">
                    <div class="p-4 border-l-4 border-green-600 bg-green-50 rounded-lg">
                        <p>
                            Untuk jenis kelamin <b>laki-laki</b>, kelompok umur
                            <b>{{ $laki_summary['max_group'] }}</b> adalah yang
                            <b>tertinggi</b>
                            dengan jumlah <b>{{ $laki_summary['max_value'] }}
                                orang</b> atau
                            <b>{{ $laki_summary['max_percent'] }}%</b>.
                            Sedangkan kelompok umur <b>{{ $laki_summary['min_group'] }}</b>
                            adalah yang <b>terendah</b> dengan jumlah
                            <b>{{ $laki_summary['min_value'] }} orang</b> atau
                            <b>{{ $laki_summary['min_percent'] }}%</b>.
                        </p>
                    </div>

                    <div class="p-4 border-l-4 border-pink-400 bg-pink-50 rounded-lg">
                        <p>
                            Untuk jenis kelamin <b>perempuan</b>, kelompok umur
                            <b>{{ $perempuan_summary['max_group'] }}</b> adalah
                            yang <b>tertinggi</b>
                            dengan jumlah <b>{{ $perempuan_summary['max_value'] }} orang</b> atau
                            <b>{{ $perempuan_summary['max_percent'] }}%</b>.
                            Sedangkan kelompok umur <b>{{ $perempuan_summary['min_group'] }}</b>
                            adalah yang <b>terendah</b> dengan jumlah
                            <b>{{ $perempuan_summary['min_value'] }} orang</b>
                            atau
                            <b>{{ $perempuan_summary['min_percent'] }}%</b>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-12 bg-gray-50">
            <div>
                <h2 class="text-3xl font-extrabold text-green-800 mb-6">
                    Berdasarkan Dusun
                </h2>

                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <!-- Diagram Pie -->
                    <div class="bg-white shadow-sm rounded-2xl p-6">
                        <canvas id="dusunChart" style="max-height: 360px;"></canvas>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Keterangan:</h3>
                        <ul class="space-y-2 text-gray-800">
                            @foreach ($penduduk_dusun as $dusun)
                                <li>
                                    <span class="font-semibold">{{ $dusun['label'] }}</span>:
                                    {{ $dusun['total'] }} Jiwa
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-12 bg-gray-50">
            <div>
                <h2 class="text-3xl font-extrabold text-green-800 mb-6">
                    Berdasarkan Pendidikan
                </h2>

                <div class="bg-white shadow-md rounded-2xl p-6">
                    <canvas id="pendidikanChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </section>

        <section class="mt-12 bg-gray-50">
            <div>
                <h2 class="text-3xl font-extrabold text-green-800 mb-6">
                    Berdasarkan Pekerjaan
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- TABEL PEKERJAAN SCROLLABLE -->
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-green-700 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold uppercase tracking-wide">
                                        Jenis Pekerjaan
                                    </th>
                                    <th class="px-6 py-3 text-right font-semibold uppercase tracking-wide">
                                        Jumlah
                                    </th>
                                </tr>
                            </thead>
                        </table>

                        <!-- Scrollable tbody -->
                        <div class="max-h-[340px] overflow-y-auto scroll-thin">
                            <style>
                                /* Scrollbar elegan dan transparan */
                                .scroll-thin::-webkit-scrollbar {
                                    width: 6px;
                                }

                                .scroll-thin::-webkit-scrollbar-thumb {
                                    background: rgba(16, 124, 16, 0.35);
                                    border-radius: 10px;
                                }

                                .scroll-thin::-webkit-scrollbar-thumb:hover {
                                    background: rgba(16, 124, 16, 0.55);
                                }

                                .scroll-thin::-webkit-scrollbar-track {
                                    background: transparent;
                                }
                            </style>

                            <table class="min-w-full text-sm">
                                <tbody class="divide-y divide-gray-200 text-gray-800 bg-white">
                                    @foreach ($pekerjaan_counts as $job => $count)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-3">{{ $job }}</td>
                                            <td class="px-6 py-3 text-right font-semibold">
                                                {{ $count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- KARTU RINGKASAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($pekerjaan_top6 as $job => $count)
                            <div
                                class="bg-white rounded-xl shadow-sm p-5 flex flex-col justify-between hover:shadow-lg transition">
                                <p class="text-gray-700 font-medium">{{ $job }}</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">
                                    {{ $count }}</p>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        <section class="mt-12">
            <h2 class="text-2xl font-bold text-green-800 mb-4">
                Berdasarkan Wajib Pilih
            </h2>

            <div class="bg-white rounded-2xl shadow p-6" style="height: 420px;">
                {{--
                tambahkan tinggi di sini --}}
                <canvas id="wajibPilihChart"></canvas>
            </div>
        </section>

        <section class="mt-12 bg-gray-50">
            <div>
                <h2 class="text-3xl font-bold text-teal-700 mb-6">
                    Berdasarkan Perkawinan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($perkawinan as $item)
                        <div class="bg-white p-6 rounded-xl border border-gray-200 flex items-center space-x-4">
                            <div class="shrink-0">
                                <img src="{{ $item['icon'] }}" alt="{{ $item['title'] }}" class="h-14 w-14">
                            </div>
                            <div>
                                <p class="text-gray-500 text-base">{{ $item['title'] }}</p>
                                <p class="text-teal-700 text-3xl font-bold">
                                    {{ number_format($item['value'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <section class="mt-12 mb-12 bg-gray-50">
            <div>
                <h2 class="text-3xl font-bold text-teal-700 mb-6">
                    Berdasarkan Agama
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($dataAgama as $item)
                        <div class="bg-white p-6 rounded-xl border border-gray-200 flex items-center space-x-4">
                            <div class="shrink-0">
                                <img src="{{ $item['icon'] }}" alt="{{ $item['title'] }}" class="h-14 w-14">
                            </div>
                            <div>
                                <p class="text-gray-500 text-base">{{ $item['title'] }}</p>
                                <p class="text-teal-700 text-3xl font-bold">
                                    {{ number_format($item['value'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </section>
</div>

@push('styles')
    <style>
        .tab-active {
            border-bottom: 3px solid #15803d;
            color: #15803d;
        }
    </style>
@endpush

@assets
    <script src="{{ asset('js/chart.js@3.0.0.min.js') }}"></script>
    <script src="{{ asset('js/chartjs-plugin-datalabels@2.0.0.min.js') }}"></script>
@endassets

@script
    <script>
        const umurCanvas = document.getElementById("umurChart");
        const pendudukUmur = @json($penduduk_umur);
        // === CHART 1: Umur ===
        if (umurCanvas) {
            // Hitung nilai maksimum untuk skala simetris
            const maxL = Math.max(...pendudukUmur.map(p => p.L));
            const maxP = Math.max(...pendudukUmur.map(p => p.P));
            const maxValue = Math.max(maxL, maxP);

            new Chart(umurCanvas.getContext("2d"), {
                type: "bar",
                data: {
                    labels: pendudukUmur.map(p => p.kelompok),
                    datasets: [{
                            label: "Laki-Laki",
                            data: pendudukUmur.map(p => p.L * -1),
                            backgroundColor: "#047857",
                            borderRadius: 6,
                            barThickness: 10
                        },
                        {
                            label: "Perempuan",
                            data: pendudukUmur.map(p => p.P),
                            backgroundColor: "#f9a8d4",
                            borderRadius: 6,
                            barThickness: 10
                        }
                    ]
                },
                options: {
                    indexAxis: "y",
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            suggestedMin: -maxValue, // kiri dan kanan simetris
                            suggestedMax: maxValue,
                            ticks: {
                                callback: v => Math.abs(v)
                            }
                        },
                        y: {
                            stacked: true,
                            reverse: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: "top"
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    return label + ': ' + Math.abs(context.parsed.x);
                                }
                            }
                        },
                    }
                }
            });
        }

        // === CHART 2: Dusun ===
        const dusunCanvas = document.getElementById("dusunChart");
        if (dusunCanvas) {
            const labels = @json($penduduk_dusun->pluck('label'));
            const data = @json($penduduk_dusun->pluck('total'));
            const total = data.reduce((a, b) => a + b, 0);

            new Chart(dusunCanvas, {
                type: "pie",
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            "#4f46e5", "#86efac", "#facc15", "#f87171",
                            "#38bdf8", "#d946ef", "#fb923c", "#22c55e"
                        ],
                        borderColor: "#fff",
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const value = ctx.raw;
                                    const percent = ((value / total) * 100).toFixed(2);
                                    return `${ctx.label}: ${value} Jiwa (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // === CHART 3: Pendidikan ===
        const pendidikanCanvas = document.getElementById("pendidikanChart");
        if (pendidikanCanvas) {
            const labels = @json($pendidikan_labels);
            const data = @json($pendidikan_counts);

            new Chart(pendidikanCanvas, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: "#064e3b",
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    ticks: {
                        precision: 0, // hapus .0
                        stepSize: 1 // langkah per 1
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // === CHART 4: Wajib Pilih ===
        const wajibCanvas = document.getElementById("wajibPilihChart");
        if (wajibCanvas) {
            const labels = @json(array_keys($wajib_pilih));
            const data = @json(array_values($wajib_pilih));

            new Chart(wajibCanvas, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "",
                        data: data,
                        backgroundColor: "#003b2c",
                        borderRadius: 8,
                        barThickness: 80
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: "#333",
                            anchor: "end",
                            align: "start",
                            formatter: (v) => v.toLocaleString()
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    </script>
@endscript
