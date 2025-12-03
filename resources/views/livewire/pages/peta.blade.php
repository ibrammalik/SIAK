<div class="pt-32 pb-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-4xl font-bold text-green-800 mb-4 text-center">
            Peta Wilayah Kelurahan
        </h1>
        <p class="text-center text-gray-700 max-w-3xl mx-auto mb-10">
            Peta interaktif Kelurahan {{ $nama_kelurahan }} — tampilkan batas
            Kelurahan, RW, RT, serta lokasi fasilitas umum.
        </p>
        {{-- Map --}}
        <div>
            <div id="map" class="w-full h-[500px] rounded-xl shadow-md z-0">
            </div>
        </div>
    </div>
</div>
</div>

@assets
    <link rel="stylesheet" href="{{ asset('css/leaflet@1.9.4.css') }}"/>
    <script src="{{ asset('js/leaflet@1.9.4.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/leaflet-gesture-handling@1.2.2.min.css') }}">
    <script src="{{ asset('js/leaflet-gesture-handling@1.2.2.min.js') }}"></script>

    <link rel='stylesheet' href="{{ asset('css/leaflet-fullscreen@1.0.1.css') }}"/>
    <script src="{{ asset('js/leaflet-fullscreen@1.0.1.min.js') }}"></script>
@endassets

@push('styles')
    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(16, 185, 129, 0.5);
            border-radius: 3px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(16, 185, 129, 0.8);
        }

        .legend {
            background: white;
            padding: 10px 14px;
            line-height: 1.6;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
        }

        .legend span {
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            border-radius: 3px;
        }
    </style>
@endpush

@script
    <script>
        const map = L.map("map", {
            fullscreenControl: true,
            gestureHandling: true,
        }).setView([-3.316, 102.912], 14);

        // Base layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a>'
        }).addTo(map);

        // === 1. Load Dynamic Markers ===
        const markers = @json($markers);
        let layerPlaces = [];

        if (Array.isArray(markers) && markers.length > 0) {
            markers.forEach(m => {
                if (!m.lat || !m.lng) return;

                const customIcon = L.divIcon({
                    html: m.icon_html,
                    className: "custom-div-icon", // no default Leaflet styles
                    iconSize: [30, 42],
                    iconAnchor: [15, 42],
                    popupAnchor: [0, -16]
                });

                const layer = L.marker([m.lat, m.lng], {
                        icon: customIcon
                    })
                    .bindPopup(`
                    <b>${m.name}</b><br>
                    ${m.desc ?? ''}
                `);

                layerPlaces.push(layer);
            });
        }
        layerPlaces = L.layerGroup(layerPlaces)

        // === Create groups ===
        const groupKelurahan = L.featureGroup().addTo(map);
        const groupRW = L.featureGroup().addTo(map);
        const groupRT = L.featureGroup().addTo(map);

        let bounds = L.latLngBounds();

        const layersKelurahan = @json($layersKelurahan);

        layersKelurahan.forEach(layer => {
            if (!layer.geo) return;

            const geo = JSON.parse(layer.geo);

            const polygon = L.geoJson(geo, {
                color: layer.color,
                weight: 2,
                fillColor: layer.color,
                fillOpacity: 0.3
            }).addTo(groupKelurahan);

            polygon.bindPopup(`<b>${layer.name}</b><br>Kelurahan`);

            polygon.eachLayer(l => bounds.extend(l.getBounds()));
        });

        const layersRW = @json($layersRW);

        layersRW.forEach(layer => {
            if (!layer.geo) return;
            const geo = JSON.parse(layer.geo);

            const polygon = L.geoJson(geo, {
                color: layer.color,
                weight: 1,
                fillColor: layer.color,
                fillOpacity: 0.25
            }).addTo(groupRW);

            polygon.bindPopup(`<b>${layer.name}</b><br>RW`);

            polygon.eachLayer(l => bounds.extend(l.getBounds()));
        });

        const layersRT = @json($layersRT);

        layersRT.forEach(layer => {
            if (!layer.geo) return;
            const geo = JSON.parse(layer.geo);

            const polygon = L.geoJson(geo, {
                color: layer.color,
                weight: 1,
                dashArray: '4,4',
                fillColor: layer.color,
                fillOpacity: 0.15
            }).addTo(groupRT);

            polygon.bindPopup(`<b>${layer.name}</b><br>RT`);

            polygon.eachLayer(l => bounds.extend(l.getBounds()));
        });

        // Controls
        L.control.layers(null, {
            "Kelurahan": groupKelurahan,
            "RW": groupRW,
            "RT": groupRT,
            "Tempat": layerPlaces,
        }, {
            collapsed: false
        }).addTo(map);

        if (bounds.isValid()) {
            map.fitBounds(bounds, {
                padding: [20, 20]
            });
        } else {
            map.setView([-2.5489, 118.0149], 5); // Indonesia
        }
    </script>
@endscript
