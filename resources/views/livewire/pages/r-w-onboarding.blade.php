<div id="rw-onboarding" class="min-h-screen flex items-center justify-center bg-gray-50 pt-16 px-6">
    <div class="max-w-7xl w-full bg-white rounded-2xl shadow-lg p-8 space-y-6 mt-10">

        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
            <p class="text-gray-600">Mari siapkan akun Ketua RW Anda</p>
        </div>

        <!-- Form -->
        <form wire:submit="create">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-5 w-full">
                Kirim
            </x-filament::button>
        </form>

        <x-filament-actions::modals />
    </div>
</div>
