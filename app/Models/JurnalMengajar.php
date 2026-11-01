<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'jadwal_id', 'tanggal', 'mata_pelajaran', 'kelas',
        'pertemuan_ke', 'materi', 'kegiatan', 'jumlah_siswa_hadir', 'catatan', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function jadwal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_id');
    }
}
