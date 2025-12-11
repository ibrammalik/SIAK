<?php

namespace App\Livewire\Pages;

use App\Enums\UserRole;
use App\Livewire\BaseLayout;
use App\Models\RW;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;

class RWOnboarding extends BaseLayout implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $pageTitle = 'RW Onboarding';

    public ?array $data = [];

    public function mount()
    {
        if (User::where('role', UserRole::KetuaRW)->exists()) {
            return redirect()->route('filament.app.auth.login');
        }

        $this->form->fill();
    }

    protected function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Anda')
                    ->placeholder('Contoh: Budi Santoso')
                    ->helperText('Masukkan nama lengkap Anda sebagai ketua RW.')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->label('Email Login')
                    ->placeholder('contoh: ketuarw@gmail.com')
                    ->helperText('Email ini akan digunakan untuk login ke aplikasi.')
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Password Login')
                    ->placeholder('Minimal 6 karakter')
                    ->helperText('Password ini digunakan untuk login ke aplikasi.')
                    ->required(),

                TextInput::make('confirm_password')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->placeholder('Ulangi password Anda')
                    ->helperText('Harus sama dengan password di atas.')
                    ->same('password')
                    ->required(),

                TextInput::make('nomor_rw')
                    ->numeric()
                    ->label('Nomor RW')
                    ->placeholder('Contoh: 3')
                    ->helperText('Masukkan nomor RW wilayah Anda. Gunakan angka, misalnya: 3.')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create()
    {
        $data = $this->form->getState();

        if ($data['password'] !== $data['confirm_password']) {
            $this->addError('data.confirm_password', 'Password tidak cocok.');
            return;
        }

        $rw = RW::create([
            'nomor' => $data['nomor_rw'],
        ]);

        User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::KetuaRW,
            'rw_id' => $rw->id,
        ]);

        return redirect()->route('filament.app.auth.login');
    }

    public function render(): View
    {
        return $this->layoutWithData(
            view('livewire.pages.r-w-onboarding')
        );
    }
}
