<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_orang_tua',
        'no_hp_orang_tua',
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

    public function anggotaKelas(): HasMany
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function catatanWaliKelas(): HasMany
    {
        return $this->hasMany(CatatanWaliKelas::class);
    }

    public function kelasAktif(): ?AnggotaKelas
    {
        return $this->anggotaKelas()
            ->whereHas(
                'kelasAkademik.tahunAkademik',
                function ($query) {
                    $query->where('is_active', true);
                }
            )
            ->with([
                'kelasAkademik.kelas.jurusan',
                'kelasAkademik.tahunAkademik',
                'kelasAkademik.waliKelas',
            ])
            ->first();
    }
}