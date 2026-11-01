<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'mata_pelajaran', 'kelas', 'nama_siswa',
        'nis', 'jenis_penilaian', 'nilai', 'tahun_ajaran', 'semester',
        'tanggal_penilaian', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
            'tanggal_penilaian' => 'date',
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
