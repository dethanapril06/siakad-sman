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
        'bobot',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'integer',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public static function getBobotMap(): array
    {
        return static::aktif()->pluck('bobot', 'kode')->toArray();
    }
}