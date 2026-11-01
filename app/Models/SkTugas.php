<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkTugas extends Model
{
    use HasFactory;

    protected $table = 'sk_tugas';

    protected $fillable = [
        'guru_id', 'sekolah_id', 'no_sk', 'judul_sk', 'jenis_sk',
        'tanggal_sk', 'berlaku_mulai', 'berlaku_sampai',
        'file_path', 'keterangan', 'diterbitkan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'berlaku_mulai' => 'date',
            'berlaku_sampai' => 'date',
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

    public function penerbit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}
