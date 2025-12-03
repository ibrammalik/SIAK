<div id="profil-page" class="pt-32 max-w-7xl mx-auto px-6 space-y-16">

    {{-- SECTION 1: PROFIL UMUM --}}
    <section>
        <h1 class="text-3xl font-bold text-green-800 mb-4 text-center uppercase">
            Profil {{ $nama_kelurahan }}
        </h1>
        <p class="text-gray-700 leading-relaxed">
            {{ $nama_kelurahan }} merupakan salah satu
            kelurahan
            yang berada di wilayah {{ $kecamatan }},
            {{ $kota }}.
            Kelurahan ini memiliki luas wilayah sekitar {{ $luas_wilayah }}
            hektar
            dengan jumlah penduduk
            {{ $jumlah_penduduk }} jiwa. Wilayah ini terdiri
            dari {{ $jumlah_rw }}
            RW dan {{ $jumlah_rt }} RT,
            serta memiliki fasilitas umum seperti sekolah, tempat ibadah,
            dan pusat
            layanan masyarakat.
        </p>
    </section>

    {{-- SECTION 2: VISI & MISI --}}
    <section>
        <h2 class="text-2xl font-bold text-green-800 mb-4">Visi & Misi</h2>

        <div class="bg-white rounded-2xl shadow p-6 space-y-4">
            <div>
                <h3 class="font-semibold text-green-700 mb-2">Visi:</h3>
                <div class="prose fi-prose max-w-full">
                    @if (!empty($visi))
                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($visi) }}
                    @else
                        <p>
                            “Terwujudnya {{ $nama_kelurahan }}
                            yang mandiri,
                            maju, dan sejahtera
                            melalui pelayanan publik yang transparan dan
                            partisipatif.”
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-green-700 mb-2">Misi:</h3>
                <div class="prose fi-prose max-w-full">
                    @if (!empty($misi))
                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($misi) }}
                    @else
                        <ul>
                            <li>
                                <p>Meningkatkan pelayanan publik yang cepat dan
                                    berkualitas.</p>
                            </li>
                            <li>
                                <p>Memberdayakan masyarakat melalui kegiatan ekonomi
                                    dan sosial.</p>
                            </li>
                            <li>
                                <p>Menjaga kebersihan, keamanan, dan ketertiban
                                    lingkungan.</p>
                            </li>
                            <li>
                                <p>Meningkatkan partisipasi masyarakat dalam
                                    pembangunan.</p>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 3: STRUKTUR ORGANISASI --}}
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-green-800 mb-6 text-center">
            Struktur Organisasi Pemerintahan
        </h2>

        <div
            class="flex justify-center items-center rounded-2xl overflow-hidden shadow border border-gray-100 bg-white">
            @if (!isset($struktur_organisasi))
                <img src="{{ asset('images/contoh_struktur_organisasi_kelurahan.png') }}"
                    alt="struktur organisasi kelurahan kalicari" class="h-[400px] object-contain">
            @else
                <img src="{{ 'storage/' . $struktur_organisasi }}" alt="struktur organisasi kelurahan kalicari"
                    class="h-[400px] object-contain">
            @endif
        </div>
    </section>

    {{-- SECTION 4: PETA KELURAHAN --}}
    <section>
        <h2 class="text-2xl font-extrabold text-green-800 mb-6">Peta Lokasi
            Kelurahan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri: Info Kelurahan -->
            <div class="bg-white rounded-2xl shadow p-6 border border-gray-100 flex flex-col justify-between">

                <!-- Batas Kelurahan -->
                <div>
                    <p class="font-semibold text-gray-800 mb-2">Batas
                        Kelurahan:</p>
                    <div class="grid grid-cols-2 text-sm text-gray-700 gap-y-2">
                        <div>
                            <p class="font-bold">Utara</p>
                            <p>{{ $batas['utara'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-bold">Timur</p>
                            <p>{{ $batas['timur'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-bold">Selatan</p>
                            <p>{{ $batas['selatan'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-bold">Barat</p>
                            <p>{{ $batas['barat'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-gray-200">

                <!-- Luas Kelurahan -->
                <div class="flex justify-between text-sm text-gray-700 mb-2">
                    <p class="font-semibold">Luas Kelurahan:</p>
                    <p>{{ $luas_wilayah }} hectare</p>
                </div>

                <!-- Jumlah Penduduk -->
                <div class="flex justify-between text-sm text-gray-700">
                    <p class="font-semibold">Jumlah Penduduk:</p>
                    <p>{{ $jumlah_penduduk }} Jiwa</p>
                </div>
            </div>

            <!-- Kolom Kanan: Peta -->
            <div id="map" class="rounded-2xl shadow border border-gray-100 h-[300px] z-19">
            </div>
        </div>
    </section>
</div>

@assets
    <link rel="stylesheet" href="{{ asset('css/leaflet@1.9.4.css') }}"/>
    <script src="{{ asset('js/leaflet@1.9.4.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/leaflet-gesture-handling@1.2.2.min.css') }}">
    <script src="{{ asset('js/leaflet-gesture-handling@1.2.2.min.js') }}"></script>

    <link rel='stylesheet' href="{{ asset('css/leaflet-fullscreen@1.0.1.css') }}"/>
    <script src="{{ asset('js/leaflet-fullscreen@1.0.1.min.js') }}"></script>
@endassets

{{-- map script --}}
@script
    <script>
        const map = L.map("map", {
            gestureHandling: true,
            fullscreenControl: true,
        });

        // Default Indonesia view
        const defaultView = {
            lat: -2.5489,
            lng: 118.0149,
            zoom: 5
        };

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "© OpenStreetMap"
        }).addTo(map);

        const geoJsonRaw = @json($geoLayer);
        const layerColor = "{{ $layerColor }}";

        let hasPolygon = false;

        // === 1. Load Polygon (if exists & valid) ===
        let kelurahanLayer = null;

        if (geoJsonRaw) {
            try {
                const geoJson = JSON.parse(geoJsonRaw);

                // skip empty objects {}
                if (Object.keys(geoJson).length > 0) {
                    hasPolygon = true;

                    kelurahanLayer = L.geoJSON(geoJson, {
                            style: {
                                color: layerColor,
                                weight: 2,
                                fillColor: layerColor,
                                fillOpacity: 0.3
                            }
                        })
                        .addTo(map)
                        .bindPopup(`
                        <div class='p-2 text-sm'>
                            <h3 class='font-bold text-green-700 text-lg mb-1'>
                                Kelurahan {{ $nama_kelurahan }}
                            </h3>
                            <p class='text-gray-700 mb-2'>
                                Kecamatan Pedurungan<br>
                                Kota Semarang
                            </p>
                            <a href='{{ route('profil') }}'
                                class='inline-block bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1 rounded-md transition'>
                                Lihat Profil
                            </a>
                        </div>
                    `);

                    kelurahanLayer.on("click", e =>
                        kelurahanLayer.openPopup(e.latlng)
                    );

                    kelurahanLayer.on("mouseover", () =>
                        kelurahanLayer.setStyle({
                            fillOpacity: 0.5
                        })
                    );

                    kelurahanLayer.on("mouseout", () =>
                        kelurahanLayer.setStyle({
                            fillOpacity: 0.3
                        })
                    );
                }
            } catch (e) {
                console.warn("Invalid GeoJSON:", e);
            }
        }

        // === 2. Smart Map Positioning ===
        if (hasPolygon) {
            // Fit polygon
            map.fitBounds(kelurahanLayer.getBounds(), {
                padding: [20, 20]
            });

            setTimeout(() => {
                map.flyToBounds(kelurahanLayer.getBounds(), {
                    duration: 1.2
                });
            }, 400);

        } else {
            // Default Indonesia map
            map.setView([defaultView.lat, defaultView.lng], defaultView.zoom);
        }
    </script>
@endscript
