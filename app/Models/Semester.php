<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = [
        'tahun_akademik_id',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'is_rapor_open',
        'tanggal_rapor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
            'is_rapor_open' => 'boolean',
            'tanggal_rapor' => 'date',
        ];
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mengajars(): HasMany
    {
        return $this->hasMany(Mengajar::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function getNamaLengkapAttribute(): string
    {
        $label = ucfirst($this->nama) . ' - ' . ($this->tahunAkademik?->nama ?? '');

        return $this->is_active ? "{$label} (Aktif)" : $label;
    }
}