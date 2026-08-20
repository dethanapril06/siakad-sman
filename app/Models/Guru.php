<?php

namespace App\Models;

use App\Models\KelasAkademik;
use App\Models\Mengajar;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(
            KelasAkademik::class,
            'wali_kelas_id'
        );
    }

    public function mengajars(): HasMany
    {
        return $this->hasMany(Mengajar::class);
    }

    public function isWaliKelas(): bool
    {
        return $this->kelasWali()->exists();
    }

    public function isWaliKelasAktif(): bool
    {
        return $this->kelasWali()
            ->whereHas('tahunAkademik', function ($query) {
                $query->where('is_active', true);
            })
            ->exists();
    }

    public function kelasWaliAktif(): ?KelasAkademik
    {
        return $this->kelasWali()
            ->whereHas('tahunAkademik', function ($query) {
                $query->where('is_active', true);
            })
            ->with([
                'kelas.jurusan',
                'tahunAkademik',
            ])
            ->first();
    }

    public function isKepalaSekolah(): bool
    {
        return $this->user?->isKepalaSekolah() ?? false;
    }
}