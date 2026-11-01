@extends('layouts.app')
@section('title','Tambah Jadwal Mengajar')
@section('page-title','Tambah Jadwal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Tambah Jadwal Mengajar</h1></div>
    <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:720px;">
    <div class="card">
        <form method="POST" action="{{ route('jadwal-mengajar.store') }}">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Guru <span class="req">*</span></label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->user->name }} ({{ $g->jenjang }} – {{ $g->spesialisasi }})</option>
                        @endforeach
                    </select>
                    @error('guru_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Sekolah <span class="req">*</span></label>
                    <select name="sekolah_id" class="form-control" required>
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}" {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" placeholder="Mis: Matematika" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Mis: VII-A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hari <span class="req">*</span></label>
                    <select name="hari" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan') }}" placeholder="Mis: Lab Komputer">
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span class="req">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai <span class="req">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="req">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran','2025/2026') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester <span class="req">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil" {{ old('semester','Ganjil') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
