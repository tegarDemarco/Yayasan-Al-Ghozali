<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'mata_pelajaran', 'kelas', 'hari',
        'jam_mulai', 'jam_selesai', 'ruangan', 'tahun_ajaran', 'semester', 'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'jam_mulai' => 'datetime:H:i',
            'jam_selesai' => 'datetime:H:i',
            'is_aktif' => 'boolean',
        ];
    }

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
