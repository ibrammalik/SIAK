<?php

namespace App\Livewire\Pages;

use App\Enums\UserRole;
use App\Livewire\BaseLayout;
use App\Models\RW;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RWOnboarding extends BaseLayout
{
    protected string $pageTitle = 'RW Onboarding';
    public $user_name, $user_email, $user_password, $nomor_rw;

    protected $rules = [
        'user_name' => 'required|min:3',
        'user_email' => 'required|email|unique:users,email',
        'user_password' => 'required|min:6',
        'nomor_rw' => 'required'
    ];

    public function mount()
    {
        // If ketua_rw already exists, redirect to Filament login
        if (User::where('role', 'ketua_rw')->exists()) {
            return redirect()->route('filament.app.auth.login'); // default Filament login route
        }
    }

    public function register()
    {
        $this->validate();

        // Create RW
        $rw = RW::create([
            'nomor' => $this->nomor_rw,
        ]);

        // Create user
        $user = User::create([
            'name' => $this->user_name,
            'email' => $this->user_email,
            'password' => Hash::make($this->user_password),
            'role' => UserRole::KetuaRW,
            'rw_id' =>  $rw->id,
        ]);

        return redirect()->route('filament.app.auth.login');
    }

    public function render()
    {
        return $this->layoutWithData(
            view('livewire.pages.r-w-onboarding')
        );
    }
}
