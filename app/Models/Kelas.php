<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'jurusan_id',
        'tingkat',
        'nama',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function kelasAkademiks(): HasMany
    {
        return $this->hasMany(KelasAkademik::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function getNamaLengkapAttribute(): string
    {
        $jurusan = $this->jurusan?->kode;

        return trim(
            "{$this->tingkat} {$jurusan} {$this->nama}"
        );
    }
}