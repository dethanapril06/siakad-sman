<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'pertemuan_id',
        'siswa_id',
        'status',
        'keterangan',
    ];

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeSakit($query)
    {
        return $query->where('status', 'sakit');
    }

    public function scopeIzin($query)
    {
        return $query->where('status', 'izin');
    }

    public function scopeAlpa($query)
    {
        return $query->where('status', 'alpa');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }
}