<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $fillable = [
        'mengajar_id',
        'ruangan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function mengajar(): BelongsTo
    {
        return $this->belongsTo(Mengajar::class);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function getJamAttribute(): string
    {
        return substr($this->jam_mulai, 0, 5)
            . ' - '
            . substr($this->jam_selesai, 0, 5);
    }
}