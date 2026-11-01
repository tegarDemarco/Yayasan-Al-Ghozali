<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzinCuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'jenis_pengajuan', 'tanggal_mulai',
        'tanggal_selesai', 'jumlah_hari', 'alasan', 'lampiran',
        'status_approval', 'catatan_approval', 'disetujui_oleh', 'disetujui_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'disetujui_at' => 'datetime',
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

    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
