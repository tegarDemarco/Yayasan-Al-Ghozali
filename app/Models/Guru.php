<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sekolah_id',
        'nip',
        'nuptk',
        'jenjang',
        'spesialisasi',
        'status_kepegawaian',
        'pendidikan_terakhir',
        'tanggal_bergabung',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function jadwalMengajars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public function presensiGurus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PresensiGuru::class);
    }

    public function jurnalMengajars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JurnalMengajar::class);
    }

    public function rppModulAjars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RppModulAjar::class);
    }

    public function bahanAjars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BahanAjar::class);
    }

    public function penilaians(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function pengajuanIzinCutis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PengajuanIzinCuti::class);
    }

    public function rekapBebanMengajars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RekapBebanMengajar::class);
    }

    public function skTugas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SkTugas::class);
    }

    public function evaluasiKinerjas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluasiKinerja::class);
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    public function totalJamMengajar(): int
    {
        return $this->jadwalMengajars()->where('is_aktif', true)->count() * 2;
    }
}
