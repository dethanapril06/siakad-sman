<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertemuan extends Model
{
    protected $table = 'pertemuans';

    protected $fillable = [
        'mengajar_id',
        'pertemuan_ke',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
    ];

    protected function casts(): array
    {
        return [
            'pertemuan_ke' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function mengajar(): BelongsTo
    {
        return $this->belongsTo(Mengajar::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function getSudahDiabsenAttribute(): bool
    {
        $jumlahSiswa = $this->mengajar
            ?->kelasAkademik
            ?->anggotaKelas()
            ->count() ?? 0;

        if ($jumlahSiswa === 0) {
            return false;
        }

        return $this->absensis()->count()
            === $jumlahSiswa;
    }

    public function getProgresAbsensiAttribute(): string
    {
        $jumlahSiswa = $this->mengajar
            ?->kelasAkademik
            ?->anggotaKelas()
            ->count() ?? 0;

        $jumlahDiabsen = $this->absensis()->count();

        return "{$jumlahDiabsen}/{$jumlahSiswa}";
    }
}