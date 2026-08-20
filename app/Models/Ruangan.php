<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'kapasitas',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}