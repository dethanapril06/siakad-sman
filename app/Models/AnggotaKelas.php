<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKelas extends Model
{
    protected $table = 'anggota_kelas';

    protected $fillable = [
        'kelas_akademik_id',
        'siswa_id',
    ];

    public function kelasAkademik(): BelongsTo
    {
        return $this->belongsTo(KelasAkademik::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}