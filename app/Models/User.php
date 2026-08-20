<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function pegawaiTu(): HasOne
    {
        return $this->hasOne(PegawaiTu::class);
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function isPegawaiTu(): bool
    {
        return $this->hasRole('pegawai_tu');
    }

    public function isGuru(): bool
    {
        return in_array($this->role?->name, [
            'guru',
            'kepala_sekolah',
        ]);
    }

    public function isGuruMapel(): bool
    {
        return $this->hasRole('guru');
    }

    public function isSiswa(): bool
    {
        return $this->hasRole('siswa');
    }

    public function isKepalaSekolah(): bool
    {
        return $this->hasRole('kepala_sekolah');
    }
}