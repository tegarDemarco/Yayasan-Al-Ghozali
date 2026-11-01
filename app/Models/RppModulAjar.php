<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RppModulAjar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'judul', 'mata_pelajaran', 'kelas',
        'tahun_ajaran', 'semester', 'pertemuan_ke', 'file_path', 'tipe',
        'status_validasi', 'catatan_validasi', 'divalidasi_oleh', 'divalidasi_at',
    ];

    protected function casts(): array
    {
        return [
            'divalidasi_at' => 'datetime',
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

    public function validator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}
