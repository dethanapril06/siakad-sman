<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function mengajars(): HasMany
    {
        return $this->hasMany(Mengajar::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}