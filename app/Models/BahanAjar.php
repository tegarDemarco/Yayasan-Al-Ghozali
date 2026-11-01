<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanAjar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'judul', 'mata_pelajaran',
        'kelas', 'deskripsi', 'file_path', 'tipe_file', 'ukuran_file',
    ];

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->ukuran_file ?? 0;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        return round($bytes / 1024, 2).' KB';
    }
}
