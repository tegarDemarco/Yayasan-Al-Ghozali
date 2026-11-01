<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'sekolah_id', 'tahun_ajaran', 'semester',
        'nilai_kinerja', 'nilai_kehadiran', 'nilai_administrasi',
        'nilai_pembelajaran', 'nilai_akhir', 'predikat',
        'catatan', 'dievaluasi_oleh', 'dievaluasi_at',
    ];

    protected function casts(): array
    {
        return [
            'dievaluasi_at' => 'datetime',
            'nilai_kinerja' => 'decimal:2',
            'nilai_kehadiran' => 'decimal:2',
            'nilai_administrasi' => 'decimal:2',
            'nilai_pembelajaran' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
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

    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'dievaluasi_oleh');
    }
}
