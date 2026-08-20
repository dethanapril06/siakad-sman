<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisNilai extends Model
{
    protected $table = 'jenis_nilais';

    protected $fillable = [
        'kode',
        'nama',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}