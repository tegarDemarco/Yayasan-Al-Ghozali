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

    protected $appends = [
        'jam_mulai_formatted',
        'jam_selesai_formatted',
        'formatted_waktu',
    ];

    protected function casts(): array
    {
        return [
            'jam_mulai' => 'datetime:H:i',
            'jam_selesai' => 'datetime:H:i',
            'is_aktif' => 'boolean',
        ];
    }

    public function getJamMulaiFormattedAttribute(): string
    {
        if (! $this->jam_mulai) {
            return '';
        }
        return \Carbon\Carbon::parse($this->jam_mulai)->format('H:i');
    }

    public function getJamSelesaiFormattedAttribute(): string
    {
        if (! $this->jam_selesai) {
            return '';
        }
        return \Carbon\Carbon::parse($this->jam_selesai)->format('H:i');
    }

    public function getFormattedWaktuAttribute(): string
    {
        $mulai = $this->jam_mulai_formatted;
        $selesai = $this->jam_selesai_formatted;
        return ($mulai && $selesai) ? "{$mulai}–{$selesai}" : '-';
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
