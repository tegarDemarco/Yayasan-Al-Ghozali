<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapBebanMengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'tahun_ajaran', 'semester',
        'total_jam_mengajar', 'jumlah_kelas', 'jumlah_mapel',
        'status_beban', 'standar_jam_minimal', 'catatan',
    ];

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
}
