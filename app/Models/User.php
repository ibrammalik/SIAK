<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'rw_id',
        'rt_id',
        'penduduk_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // 🔗 Relasi wilayah dan penduduk

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isKelurahan(): bool
    {
        return $this->role === UserRole::AdminKelurahan;
    }

    public function isRW(): bool
    {
        return $this->role === UserRole::KetuaRW;
    }

    public function isRT(): bool
    {
        return $this->role === UserRole::KetuaRT;
    }

    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}
