@extends('layouts.app')
@section('title','Edit Data Guru')
@section('page-title','Edit Data Guru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Edit Guru: {{ $guru->user->name }}</h1>
        <p>Perbarui informasi data guru</p>
    </div>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST" action="{{ route('guru.update', $guru) }}">
    @csrf @method('PUT')
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <div class="card-title" style="margin-bottom:16px;">Informasi Akun</div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $guru->user->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $guru->user->email) }}" required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NUPTK</label>
                        <input type="text" name="nuptk" class="form-control" value="{{ old('nuptk', $guru->nuptk) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $guru->user->telepon) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="req">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['aktif','nonaktif','cuti'] as $s)
                            <option value="{{ $s }}" {{ old('status',$guru->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $guru->user->alamat) }}</textarea>
                </div>
            </div>
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">Data Kepegawaian</div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Sekolah <span class="req">*</span></label>
                        <select name="sekolah_id" class="form-control" required>
                            @foreach($sekolahs as $sekolah)
                            <option value="{{ $sekolah->id }}" {{ old('sekolah_id',$guru->sekolah_id) == $sekolah->id ? 'selected' : '' }}>
                                {{ $sekolah->nama_sekolah }} ({{ $sekolah->jenjang }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                        <input type="text" name="spesialisasi" class="form-control" value="{{ old('spesialisasi',$guru->spesialisasi) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Kepegawaian <span class="req">*</span></label>
                        <select name="status_kepegawaian" class="form-control" required>
                            @foreach(['PNS','GTY','GTT','Honorer'] as $s)
                            <option value="{{ $s }}" {{ old('status_kepegawaian',$guru->status_kepegawaian) === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="form-control">
                            @foreach(['SMA/SMK','D3','S1','S2','S3'] as $p)
                            <option value="{{ $p }}" {{ old('pendidikan_terakhir',$guru->pendidikan_terakhir) === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="tanggal_bergabung" class="form-control" value="{{ old('tanggal_bergabung', $guru->tanggal_bergabung?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="position:sticky;top:calc(var(--header-height) + 20px);">
            <div class="card-title" style="margin-bottom:16px;">Informasi Guru</div>
            <div style="text-align:center;padding:16px 0;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:26px;margin:0 auto 12px;">
                    {{ strtoupper(substr($guru->user->name, 0, 2)) }}
                </div>
                <div style="font-size:15px;font-weight:700;">{{ $guru->user->name }}</div>
                <div style="font-size:12.5px;color:var(--text-muted);">{{ $guru->user->email }}</div>
                <div style="margin-top:8px;"><span class="jenjang-{{ strtolower($guru->jenjang) }}">{{ $guru->jenjang }}</span></div>
            </div>
            <div style="padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan Perubahan</button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:8px;">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
