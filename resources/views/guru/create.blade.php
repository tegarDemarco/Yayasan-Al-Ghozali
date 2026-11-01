@extends('layouts.app')
@section('title','Tambah Guru')
@section('page-title','Tambah Guru Baru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Tambah Guru Baru</h1>
        <p>Isi data lengkap guru yang akan ditambahkan ke sistem</p>
    </div>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali
    </a>
</div>

<form method="POST" action="{{ route('guru.store') }}">
    @csrf
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <div class="card-title" style="margin-bottom:16px;">Informasi Akun</div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Nama lengkap guru" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" placeholder="email@alghozaly.sch.id" required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Username untuk login">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap guru">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">Data Kepegawaian</div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Sekolah <span class="req">*</span></label>
                        <select name="sekolah_id" class="form-control" required>
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($sekolahs as $sekolah)
                            <option value="{{ $sekolah->id }}" {{ old('sekolah_id') == $sekolah->id ? 'selected' : '' }}>
                                {{ $sekolah->nama_sekolah }} ({{ $sekolah->jenjang }})
                            </option>
                            @endforeach
                        </select>
                        @error('sekolah_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mata Pelajaran / Spesialisasi <span class="req">*</span></label>
                        <input type="text" name="spesialisasi" class="form-control" value="{{ old('spesialisasi') }}" placeholder="Mis: Matematika, Bahasa Arab" required>
                        @error('spesialisasi')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NUPTK</label>
                        <input type="text" name="nuptk" class="form-control" value="{{ old('nuptk') }}" placeholder="Nomor Unik PTK">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Kepegawaian <span class="req">*</span></label>
                        <select name="status_kepegawaian" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['PNS','GTY','GTT','Honorer'] as $status)
                            <option value="{{ $status }}" {{ old('status_kepegawaian') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['SMA/SMK','D3','S1','S2','S3'] as $pend)
                            <option value="{{ $pend }}" {{ old('pendidikan_terakhir') === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="tanggal_bergabung" class="form-control" value="{{ old('tanggal_bergabung') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="position:sticky;top:calc(var(--header-height) + 20px);">
            <div class="card-title" style="margin-bottom:16px;">Panduan</div>
            <div style="font-size:13px;color:var(--text-secondary);line-height:1.7;">
                <p style="margin-bottom:10px;">📋 <strong>NIP</strong> – Wajib diisi untuk PNS, opsional untuk GTY/GTT</p>
                <p style="margin-bottom:10px;">🔑 <strong>Username</strong> – Jika dikosongkan, email digunakan untuk login</p>
                <p style="margin-bottom:10px;">🏫 <strong>Sekolah</strong> – Menentukan jenjang guru secara otomatis</p>
                <p style="margin-bottom:10px;">📚 <strong>Spesialisasi</strong> – Mata pelajaran yang diajarkan</p>
            </div>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                    Simpan Data Guru
                </button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:8px;">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
