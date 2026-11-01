@extends('layouts.app')
@section('title','Tambah Penilaian')
@section('page-title','Tambah Penilaian')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Input Penilaian Siswa</h1></div>
    <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:680px;">
    <div class="card">
        <form method="POST" action="{{ route('penilaian.store') }}">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Siswa <span class="req">*</span></label>
                    <input type="text" name="nama_siswa" class="form-control" value="{{ old('nama_siswa') }}" placeholder="Nama lengkap siswa" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" placeholder="Nomor Induk Siswa">
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran', $guru?->spesialisasi) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Mis: VII-A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Penilaian <span class="req">*</span></label>
                    <select name="jenis_penilaian" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Ulangan Harian','UTS','UAS','Tugas','Praktik','Portofolio'] as $j)
                        <option value="{{ $j }}" {{ old('jenis_penilaian') === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai <span class="req">*</span> <span style="color:var(--text-muted);font-weight:400;">(0–100)</span></label>
                    <input type="number" name="nilai" class="form-control" value="{{ old('nilai') }}" min="0" max="100" step="0.01" required>
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
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Tanggal Penilaian <span class="req">*</span></label>
                    <input type="date" name="tanggal_penilaian" class="form-control" style="max-width:240px;" value="{{ old('tanggal_penilaian', date('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
