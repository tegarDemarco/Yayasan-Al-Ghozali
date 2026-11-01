<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'jenjang',
        'sekolah_id',
        'nip',
        'jabatan',
        'foto',
        'telepon',
        'alamat',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function guru(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function isPengurusYayasan(): bool
    {
        return $this->role === 'pengurus_yayasan';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isAdminSekolah(): bool
    {
        return $this->role === 'admin_sekolah';
    }
}
