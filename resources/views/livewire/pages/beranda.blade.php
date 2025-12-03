<div id="beranda-page">
    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
            transform: scale(0);
            animation: ripple-effect 0.6s linear forwards;
        }

        @keyframes ripple-effect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>

    {{-- ========================================================= --}}
    {{-- SECTION: Hero Banner --}}
    {{-- ========================================================= --}}
    <section class="flex items-center justify-center w-full h-screen">
        @if (!isset($hero_bg))
            <img src="{{ asset('images/hero-bg.webp') }}"
                class="absolute inset-0 w-full h-full object-cover brightness-50">

            <!-- Image Attribution -->
            <div class="absolute bottom-2 right-2 text-white text-xs opacity-70">
                <a target="_blank"
                    href="https://www.freepik.com/free-photo/beautiful-shot-grassy-hills-covered-trees-near-mountains-dolomites-italy_11527510.htm#fromView=keyword&page=1&position=11&uuid=a25e9648-9bdc-40f8-9090-034bbe11fe5c&query=Wallpaper">Image
                    by wirestock on Freepik</a>
            </div>
        @else
            <img src="{{ 'storage/' . $hero_bg }}" class="absolute inset-0 w-full h-full object-cover brightness-50">
        @endif

        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold text-white mb-6">
                Selamat Datang di<br>{{ $nama_kelurahan }}
            </h1>
            <p class="text-lg text-white max-w-2xl mx-auto mb-8">
                Pusat informasi masyarakat, dan transparansi
                penduduk dan demografi {{ $nama_kelurahan }}.
            </p>
            <button id="btn-jelajahi"
                class="relative overflow-hidden select-none touch-none bg-green-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-green-800 transition hover:cursor-pointer">
                Jelajahi
            </button>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION: Jelajahi Kelurahan --}}
    {{-- ========================================================= --}}
    <section id="jelajahi" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-green-800 mb-6">
                        JELAJAHI KELURAHAN
                    </h2>
                    <p class="text-gray-700 text-lg leading-relaxed mb-8">
                        Melalui website ini Anda dapat menjelajahi segala hal
                        yang terkait dengan Aspek penduduk, demografi kelurahan.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <a wire:navigate.hover href="{{ route('profil') }}"
                        class="group bg-white border border-gray-200 rounded-2xl shadow-md p-8 flex flex-col items-center hover:shadow-lg transition">
                        <x-heroicon-o-building-office
                            class="w-14 h-14 text-green-700 group-hover:scale-110 transition" />
                        <h3 class="mt-4 font-semibold text-green-800 text-center">
                            PROFIL
                            KELURAHAN</h3>
                    </a>

                    <a wire:navigate.hover href="{{ route('infografis') }}"
                        class="group bg-white border border-gray-200 rounded-2xl shadow-md p-8 flex flex-col items-center hover:shadow-lg transition">
                        <x-heroicon-o-chart-bar class="w-14 h-14 text-green-700 group-hover:scale-110 transition" />
                        <h3 class="mt-4 font-semibold text-green-800 text-center">
                            INFOGRAFIS
                        </h3>
                    </a>

                    <a target="_blank" href="{{ route('preview.monografi') }}"
                        class="group bg-white border border-gray-200 rounded-2xl shadow-md p-8 flex flex-col items-center hover:shadow-lg transition">
                        <x-heroicon-o-table-cells class="w-14 h-14 text-green-700 group-hover:scale-110 transition" />
                        <h3 class="mt-4 font-semibold text-green-800 text-center">
                            MONOGRAFI
                        </h3>
                    </a>

                    <a wire:navigate.hover href="{{ route('peta') }}"
                        class="group bg-white border border-gray-200 rounded-2xl shadow-md p-8 flex flex-col items-center hover:shadow-lg transition">
                        <x-heroicon-o-map class="w-14 h-14 text-green-700 group-hover:scale-110 transition" />
                        <h3 class="mt-4 font-semibold text-green-800 text-center">
                            PETA</h3>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION: Administrasi Penduduk --}}
    {{-- ========================================================= --}}
    <section id="penduduk" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-extrabold text-green-800 mb-4">
                    Administrasi Penduduk
                </h2>
                <p class="text-gray-700 max-w-2xl mx-auto text-lg leading-relaxed">
                    Sistem digital yang berfungsi mempermudah pengelolaan data
                    dan
                    informasi
                    terkait kependudukan dan pendayagunaannya untuk pelayanan
                    publik
                    yang
                    efektif dan efisien.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 text-white font-bold">
                <div class="grid grid-cols-2 bg-linear-to-r from-green-700 to-green-500 rounded-lg shadow">
                    <div class="text-4xl md:text-5xl text-center py-6 border-r border-green-400 count-up"
                        data-target="{{ $totalPenduduk }}">0
                    </div>
                    <div class="flex items-center justify-center text-lg md:text-xl text-green-50">
                        Penduduk</div>
                </div>
                <div class="grid grid-cols-2 bg-linear-to-r from-green-700 to-green-500 rounded-lg shadow">
                    <div class="text-4xl md:text-5xl text-center py-6 border-r border-green-400 count-up"
                        data-target="{{ $totalLaki }}">
                        0</div>
                    <div class="flex items-center justify-center text-lg md:text-xl text-green-50">
                        Laki-Laki</div>
                </div>
                <div class="grid grid-cols-2 bg-linear-to-r from-green-700 to-green-500 rounded-lg shadow">
                    <div class="text-4xl md:text-5xl text-center py-6 border-r border-green-400 count-up"
                        data-target="{{ $totalKepalaKeluarga }}">
                        0</div>
                    <div class="flex items-center justify-center text-lg md:text-xl text-green-50">
                        Kepala Keluarga</div>
                </div>
                <div class="grid grid-cols-2 bg-linear-to-r from-green-700 to-green-500 rounded-lg shadow">
                    <div class="text-4xl md:text-5xl text-center py-6 border-r border-green-400 count-up"
                        data-target="{{ $totalPerempuan }}">
                        0</div>
                    <div class="flex items-center justify-center text-lg md:text-xl text-green-50">
                        Perempuan</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION: Peta Wilayah --}}
    {{-- ========================================================= --}}
    <section class="py-16 bg-white" id="peta">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-green-800 mb-2 text-center">Peta
                Kelurahan</h2>
            <p class="text-gray-600 text-center mb-8">
                Menampilkan peta kelurahan beserta wilayah RW dan RT.
            </p>

            <div id="map" class="w-full h-[480px] rounded-xl shadow-md z-19">
            </div>
        </div>
    </section>
</div>

@assets
    <link rel="stylesheet" href="{{ asset('css/leaflet@1.9.4.css') }}"/>
    <script src="{{ asset('js/leaflet@1.9.4.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/leaflet-gesture-handling@1.2.2.min.css') }}">
    <script src="{{ asset('js/leaflet-gesture-handling@1.2.2.min.js') }}"></script>

    <script src="{{ asset('js/aos@2.3.1.min.js') }}"></script>

    <link rel='stylesheet' href="{{ asset('css/leaflet-fullscreen@1.0.1.css') }}"/>
    <script src="{{ asset('js/leaflet-fullscreen@1.0.1.min.js') }}"></script>
@endassets

{{-- animation & navbar script --}}
@script
    <script>
        const navbar = document.querySelector("nav");
        if (!navbar) return;

        const handleScroll = () => {
            if (window.scrollY > 0) {
                navbar.classList.remove("bg-transparent");
                navbar.classList.add("bg-green-700");
            } else {
                navbar.classList.remove("bg-green-700");
                navbar.classList.add("bg-transparent");
            }
        };

        window.addEventListener("scroll", handleScroll);

        AOS.init({
            duration: 1000,
            once: true
        });

        const counters = document.querySelectorAll('.count-up');
        const options = {
            threshold: 0.5
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = +el.dataset.target;
                    let start = 0;
                    const increment = target / 60; // durasi 1 detik (60 frame)
                    const timer = setInterval(() => {
                        start += increment;
                        if (start >= target) {
                            el.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            el.textContent = Math.floor(start).toLocaleString();
                        }
                    }, 16);
                    observer.unobserve(el);
                }
            });
        }, options);

        counters.forEach(counter => observer.observe(counter));
    </script>
@endscript

{{-- app dashboard script --}}
@script
    <script>
        const btn = document.getElementById("btn-jelajahi");
        const HOLD_TIME = 2000;

        let longPressTimer = null;
        let rippleInterval = null;
        let pressed = false;

        // Block long-press selection / context menu
        btn.addEventListener("contextmenu", e => e.preventDefault());
        btn.style.userSelect = "none";

        function createRipple(e) {
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement("span");

            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + "px";

            let x = e.clientX - rect.left - size / 2;
            let y = e.clientY - rect.top - size / 2;

            // fallback posisi ripple jika pointer luar tombol (mobile)
            if (isNaN(x) || isNaN(y)) {
                x = rect.width / 2 - size / 2;
                y = rect.height / 2 - size / 2;
            }

            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            ripple.className = "ripple";
            btn.appendChild(ripple);

            setTimeout(() => ripple.remove(), 800);
        }

        btn.addEventListener("pointerdown", async (e) => {
            pressed = true;
            if (navigator.vibrate) navigator.vibrate(30);
            // Start continuous ripple every 400ms
            createRipple(e);
            rippleInterval = setInterval(() => createRipple(e), 400);

            // Long press redirect
            longPressTimer = setTimeout(() => {
                if (pressed) {
                    if (navigator.vibrate) navigator.vibrate([80, 50, 80]);
                    window.location.href = "{{ route('filament.app.pages.dashboard') }}"
                }
            }, HOLD_TIME);
        });

        function stopPress() {
            pressed = false;
            clearTimeout(longPressTimer);
            clearInterval(rippleInterval);
        }

        btn.addEventListener("pointerup", () => {
            const wasPressed = pressed;
            stopPress();

            if (wasPressed) {
                // Klik biasa → scroll ke #jelajahi
                document.getElementById("jelajahi").scrollIntoView({
                    behavior: "smooth"
                });
            }
        });

        btn.addEventListener("pointerleave", stopPress);
    </script>
@endscript

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
        const kantor = @json($kantor);

        let hasMarker = false;
        let hasPolygon = false;

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

        // === 2. Load Polygon (if exists & valid) ===
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

        // === 4. Smart Map Positioning ===
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

        } else if (hasMarker) {
            // Center on marker
            map.setView([kantor.lat, kantor.lng], 15);
        } else {
            // Default Indonesia map
            map.setView([defaultView.lat, defaultView.lng], defaultView.zoom);
        }
    </script>
@endscript
