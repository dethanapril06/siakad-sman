<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanWaliKelas extends Model
{
    protected $table = 'catatan_wali_kelas';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'kelas_akademik_id',
        'catatan',
        'status_kenaikan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function kelasAkademik(): BelongsTo
    {
        return $this->belongsTo(KelasAkademik::class);
    }
}

