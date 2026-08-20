<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\TahunAkademik;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelasAkademik extends Model
{
    protected $table = 'kelas_akademiks';

    protected $fillable = [
        'kelas_id',
        'tahun_akademik_id',
        'wali_kelas_id',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(
            Guru::class,
            'wali_kelas_id'
        );
    }

    public function guru(): BelongsTo
    {
        return $this->waliKelas();
    }

    public function anggotaKelas(): HasMany
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function mengajars(): HasMany
    {
        return $this->hasMany(Mengajar::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->kelas?->nama_lengkap ?? '-';
    }
}