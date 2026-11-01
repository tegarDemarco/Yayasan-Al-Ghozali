<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah',
        'jenjang',
        'npsn',
        'akreditasi',
        'alamat',
        'telepon',
        'email',
        'logo',
        'kepala_sekolah',
    ];

    public function gurus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function jadwalMengajars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public function presensiGurus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PresensiGuru::class);
    }

    public function skTugas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SkTugas::class);
    }

    public function evaluasiKinerjas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluasiKinerja::class);
    }

    public function totalGuruAktif(): int
    {
        return $this->gurus()->where('status', 'aktif')->count();
    }

    public function persentaseKehadiran(): float
    {
        $totalPresensi = $this->presensiGurus()->count();
        if ($totalPresensi === 0) {
            return 0;
        }
        $hadir = $this->presensiGurus()->where('status', 'hadir')->count();

        return round(($hadir / $totalPresensi) * 100, 1);
    }
}
