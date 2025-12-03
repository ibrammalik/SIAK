<div id="rw-onboarding" class="min-h-screen flex items-center justify-center bg-gray-50 py-16 px-6">
    <div class="max-w-7xl w-full bg-white rounded-2xl shadow-lg p-8 space-y-6">

        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
            <p class="text-gray-600">Mari siapkan akun Ketua RW Anda</p>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="register" class="space-y-5">

            <!-- Nama -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Nama</label>
                <input type="text" wire:model="user_name" placeholder="Masukkan nama Anda"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" wire:model="user_email" placeholder="Masukkan email Anda" autocomplete="off"
                    autofill="off"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <!-- Password -->
            <div x-data="{ show: false }" class="relative">
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input :type="show ? 'text' : 'password'" wire:model="user_password" placeholder="Masukkan password"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="button" @click="show = !show"
                    class="absolute right-3 top-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <span x-show="!show">👁️</span>
                    <span x-show="show">🙈</span>
                </button>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor RW -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Nomor RW</label>
                <input type="number" wire:model="nomor_rw" placeholder="Masukkan nomor RW"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('nomor_rw')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Buat Akun Ketua RW
                </button>
            </div>

        </form>
    </div>
</div>
