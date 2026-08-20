<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademiks';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function kelasAkademiks(): HasMany
    {
        return $this->hasMany(KelasAkademik::class);
    }

    public function semesterAktif(): HasOne
    {
        return $this->hasOne(Semester::class)
            ->where('is_active', true);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}