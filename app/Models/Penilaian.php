<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penilaian extends Model
{
    protected $table = 'penilaians';

    protected $fillable = [
        'mengajar_id',
        'jenis_nilai_id',
        'judul',
        'tanggal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function mengajar(): BelongsTo
    {
        return $this->belongsTo(Mengajar::class);
    }

    public function jenisNilai(): BelongsTo
    {
        return $this->belongsTo(JenisNilai::class);
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function getSudahDinilaiAttribute(): bool
    {
        return $this->nilais()->exists();
    }

    public function getJumlahDinilaiAttribute(): int
    {
        return $this->nilais()->count();
    }
}