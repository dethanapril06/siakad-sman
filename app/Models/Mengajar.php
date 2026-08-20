<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mengajar extends Model
{
    protected $table = 'mengajars';

    protected $fillable = [
        'semester_id',
        'guru_id',
        'kelas_akademik_id',
        'mata_pelajaran_id',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelasAkademik(): BelongsTo
    {
        return $this->belongsTo(KelasAkademik::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pertemuans(): HasMany
    {
        return $this->hasMany(Pertemuan::class);
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function getDeskripsiAttribute(): string
    {
        $mataPelajaran = $this->mataPelajaran?->nama ?? '-';

        $kelas = $this->kelasAkademik?->nama_lengkap ?? '-';

        return "{$mataPelajaran} - {$kelas}";
    }
}