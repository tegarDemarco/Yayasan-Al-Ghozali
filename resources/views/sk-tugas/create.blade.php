@extends('layouts.app')
@section('title','Terbitkan SK Tugas')
@section('page-title','Terbitkan SK Tugas')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Terbitkan SK Tugas</h1></div>
    <a href="{{ route('sk-tugas.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:700px;">
    <div class="card">
        <form method="POST" action="{{ route('sk-tugas.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor SK <span class="req">*</span></label>
                    <input type="text" name="no_sk" class="form-control {{ $errors->has('no_sk')?'is-invalid':'' }}" value="{{ old('no_sk') }}" placeholder="Mis: SK/001/YAG/2025" required>
                    @error('no_sk')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Guru <span class="req">*</span></label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->user->name }} ({{ $g->jenjang ?? $g->sekolah?->jenjang ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Judul SK <span class="req">*</span></label>
                <input type="text" name="judul_sk" class="form-control" value="{{ old('judul_sk') }}" placeholder="Mis: SK Pengangkatan sebagai Guru Tetap Yayasan" required>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Jenis SK <span class="req">*</span></label>
                    <select name="jenis_sk" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Pengangkatan','Pemberhentian','Mutasi','Penghargaan','Sanksi','Tugas Tambahan'] as $j)
                        <option value="{{ $j }}" {{ old('jenis_sk') === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal SK <span class="req">*</span></label>
                    <input type="date" name="tanggal_sk" class="form-control" value="{{ old('tanggal_sk', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Berlaku Mulai</label>
                    <input type="date" name="berlaku_mulai" class="form-control" value="{{ old('berlaku_mulai') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="date" name="berlaku_sampai" class="form-control" value="{{ old('berlaku_sampai') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">File SK (PDF) <span style="color:var(--text-muted);font-weight:400;">– Maks. 5MB</span></label>
                <input type="file" name="file" class="form-control" accept=".pdf">
            </div>
            <div class="form-group" style="background:#f8fafc;padding:14px 16px;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:20px;">
                <label class="form-label" style="font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                    <span>Status Publikasi Dokumen SK</span>
                </label>
                <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;">
                        <input type="radio" name="status_publikasi" value="terpublikasi" {{ old('status_publikasi', 'terpublikasi') === 'terpublikasi' ? 'checked' : '' }} style="accent-color:var(--forest-700);">
                        <span>Publikasikan Langsung ke Guru</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;">
                        <input type="radio" name="status_publikasi" value="draft" {{ old('status_publikasi') === 'draft' ? 'checked' : '' }} style="accent-color:var(--forest-700);">
                        <span>Simpan sebagai Draft</span>
                    </label>
                </div>
                <div style="font-size:11.5px;color:var(--text-muted);margin-top:6px;">
                    Jika disimpan sebagai Draft, SK baru terlihat oleh admin/penerbit dan belum muncul di akun guru hingga dipublikasikan.
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan SK Tugas</button>
                <a href="{{ route('sk-tugas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
