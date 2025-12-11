<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }} - SIAK {{ $nama_kelurahan }}</title>

    <!-- Styles / Scripts -->
    <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/brands.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/solid.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles

    <!-- Filament Styles -->
    @filamentStyles
    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}">
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- Navbar --}}
    <nav x-data="{ open: false }"
        class="fixed w-full z-50 {{ request()->routeIs('beranda') ? 'bg-transparent' : 'bg-green-700' }} transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" wire:navigate.hover
                class="flex items-center gap-2 text-1xl font-semibold text-white hover:text-green-200 transition">
                <!-- SVG Logo -->
                <img src="/images/logo.svg" alt="Logo" class="h-8">

                <!-- Text -->
                <span>{{ $nama_kelurahan }}</span>
            </a>

            <!-- Desktop Menu -->
            <ul class="hidden md:flex gap-6 font-medium">
                @php
                    $links = ['beranda', 'profil', 'infografis', 'peta', 'kontak'];
                @endphp
                @foreach ($links as $link)
                    <li>
                        <a wire:navigate.hover href="{{ route($link) }}"
                            class="text-white border-b-2 {{ request()->routeIs($link) ? 'border-white' : 'border-transparent' }} hover:border-white transition capitalize">
                            {{ $link }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Mobile Menu Button -->
            <button @click="open = !open" class="md:hidden text-white text-2xl focus:outline-none">
                <i :class="open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'"></i>
            </button>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="open" x-transition class="md:hidden bg-green-700 text-white px-6 pb-4 space-y-3">
            @foreach ($links as $link)
                <a wire:navigate.hover href="{{ route($link) }}"
                    class="block py-2 border-b border-green-600 capitalize {{ request()->routeIs($link) ? 'font-semibold' : '' }}">
                    {{ $link }}
                </a>
            @endforeach
        </div>
    </nav>

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="relative bg-green-700 text-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Profil Kelurahan -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">
                    SIAK {{ $nama_kelurahan }}
                </h4>
                <p class="text-sm leading-relaxed">
                    {{ $deskripsi ??
                        'Sistem Informasi Administrasi Kependudukan (SIAK) Kelurahan ini menyajikan data dan informasi kependudukan secara digital, cepat, dan transparan.' }}
                </p>
            </div>

            <!-- Kontak -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">Kontak</h4>
                <ul class="space-y-2 text-sm">
                    <li>{{ $alamat }}</li>
                    <li>{{ $telepon }}</li>
                    <li>{{ $email }}</li>
                </ul>
            </div>

            <!-- Navigasi -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    @foreach ($links as $link)
                        <li><a wire:navigate.hover href="{{ route($link) }}"
                                class="hover:text-white transition capitalize">{{ $link }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Media Sosial -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">Ikuti Kami</h4>
                <div class="flex space-x-4 text-xl">
                    <a href="#" class="hover:text-green-400 transition"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="hover:text-green-400 transition"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="hover:text-green-400 transition"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="hover:text-green-400 transition"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

        </div>

        <div class="border-t border-white mt-6">
            <div class="text-center text-sm text-white py-4">
                &copy; {{ date('Y') }} {{ $nama_kelurahan }}.
                Semua hak dilindungi.
                <br>
                <span class="text-xs text-white">Dikelola oleh Sistem Informasi
                    Administrasi Kependudukan (SIAK) {{ $nama_kelurahan }}</span>
            </div>
        </div>

        @auth
            <a href="{{ route('filament.app.pages.dashboard') }}"
                class="absolute left-4 bottom-4 text-[10px] text-gray-400 opacity-50 hover:opacity-100 transition">
                App Dashboard
            </a>
        @else
            <a href="{{ route('filament.app.auth.login') }}"
                class="absolute left-4 bottom-4 text-[10px] text-gray-400 opacity-50 hover:opacity-100 transition">
                Login Pengelola
            </a>
        @endauth
    </footer>

    @stack('scripts')
    @livewireScripts
    @filamentScripts
</body>

</html>
