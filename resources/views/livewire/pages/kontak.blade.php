<div>
    <section class="pt-32 pb-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-4xl font-bold text-green-800 mb-4 text-center">
                Hubungi Kami
            </h1>

            <p class="text-center text-gray-700 max-w-2xl mx-auto mb-12">
                Kami siap melayani masyarakat dengan sepenuh hati. Silakan
                hubungi atau
                datang langsung ke kantor Kelurahan.
            </p>

            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                {{-- Kontak Info --}}
                <div class="bg-white shadow-md rounded-2xl p-8 flex flex-col justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-green-700 mb-6">
                            Informasi
                            Kontak</h2>

                        <div class="space-y-5">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 shrink-0 flex items-center justify-center bg-green-100 rounded-full text-green-700">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Alamat
                                    </h4>
                                    <p class="text-gray-600">{{ $data['alamat'] ?? 'Jalan Contoh Nomor 11' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 shrink-0 flex items-center justify-center bg-green-100 rounded-full text-green-700">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">
                                        Telepon</h4>
                                    <p class="text-gray-600">{{ $data['telepon'] ?? '023491753' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 shrink-0 flex items-center justify-center bg-green-100 rounded-full text-green-700">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Email
                                    </h4>
                                    <p class="text-gray-600">{{ $data['email'] ?? 'contoh@gmail.com' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 shrink-0 flex items-center justify-center bg-green-100 rounded-full text-green-700">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Jam
                                        Pelayanan</h4>
                                    <div class="text-gray-600 prose fi-prose">
                                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($data['jam_pelayanan'] ?? '<ul><li><p>Senin – Kamis: 08.00 – 16.00 WIB</p></li><li><p>Jumat: 07.30 – 14.00 WIB</p></li></ul>') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulir Kontak --}}
                {{-- <div class="bg-white shadow-md rounded-2xl p-8 flex flex-col justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-green-700 mb-6">
                            Kirim Pesan
                        </h2>

                        <form class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">
                                    Nama Lengkap
                                </label>
                                <input type="text" placeholder="Masukkan nama Anda"
                                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-green-500 focus:border-green-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">
                                    Email
                                </label>
                                <input type="email" placeholder="Alamat email"
                                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-green-500 focus:border-green-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">
                                    Pesan
                                </label>
                                <textarea rows="4" placeholder="Tuliskan pesan Anda..."
                                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-green-500 focus:border-green-500 outline-none"></textarea>
                            </div>

                            <button type="submit" disabled
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div> --}}

                {{-- Peta Lokasi --}}
                <div class="bg-white shadow-md rounded-2xl p-8 flex flex-col justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-green-700 mb-4">
                            Lokasi Kantor Kelurahan
                        </h2>
                        <div id="map" class="w-full h-[400px] rounded-xl shadow-md z-19">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Peta Lokasi --}}
            {{-- <div class="mt-16">
                <h2
                    class="text-2xl font-semibold text-green-700 mb-4 text-center">
                    Lokasi
                    Kantor Kelurahan</h2>
                <div id="map"
                    class="w-full h-[400px] rounded-xl shadow-md z-19">
                </div>
            </div> --}}
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

        const kantor = @json($kantor);

        let hasMarker = false;

        // === 1. Load Marker (if exists) ===
        if (kantor && kantor.lat && kantor.lng) {
            hasMarker = true;

            const customIcon = L.divIcon({
                html: kantor.icon_html,
                className: "custom-div-icon", // no default Leaflet styles
                iconSize: [30, 42],
                iconAnchor: [15, 42],
                popupAnchor: [0, -16]
            });

            const kantorMarker = L.marker([kantor.lat, kantor.lng], {
                    icon: customIcon
                })
                .bindPopup(`
                <b>${kantor.name}</b><br>
                ${kantor.desc ?? ''}
                `)
                .addTo(map)
                .openPopup();

            map.on("load", () => {
                kantorMarker.openPopup();
            });
        }

        if (hasMarker) {
            // Center on marker
            map.setView([kantor.lat, kantor.lng], 15);
        } else {
            // Default Indonesia map
            map.setView([defaultView.lat, defaultView.lng], defaultView.zoom);
        }
    </script>
@endscript
