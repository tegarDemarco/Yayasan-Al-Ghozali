@extends('layouts.app')
@section('title','Upload RPP / Modul Ajar')
@section('page-title','Upload RPP / Modul Ajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Upload RPP / Modul Ajar</h1></div>
    <a href="{{ route('rpp.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:680px;">
    <div class="card">
        <form method="POST" action="{{ route('rpp.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Tipe Dokumen <span class="req">*</span></label>
                <div style="display:flex;gap:10px;">
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;">
                        <input type="radio" name="tipe" value="RPP" {{ old('tipe','RPP') === 'RPP' ? 'checked' : '' }} required>
                        <span style="font-weight:600;">📄 RPP</span>
                        <span style="font-size:12px;color:var(--text-muted);">Rencana Pelaksanaan Pembelajaran</span>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;">
                        <input type="radio" name="tipe" value="Modul Ajar" {{ old('tipe') === 'Modul Ajar' ? 'checked' : '' }}>
                        <span style="font-weight:600;">📚 Modul Ajar</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Judul Dokumen <span class="req">*</span></label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul') }}" placeholder="Judul RPP/Modul Ajar" required>
                @error('judul')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran', $guru?->spesialisasi) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Mis: VII-A, X TKJ" required>
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
                <div class="form-group">
                    <label class="form-label">Pertemuan Ke</label>
                    <input type="number" name="pertemuan_ke" class="form-control" value="{{ old('pertemuan_ke') }}" min="1" placeholder="Nomor pertemuan">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">File Dokumen <span class="req">*</span> <span style="color:var(--text-muted);font-weight:400;">(PDF, DOC, DOCX – max 10MB)</span></label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx" required>
                @error('file')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div style="background:var(--accent-light);border:1px solid #fde68a;border-radius:var(--radius-sm);padding:12px 14px;font-size:13px;color:#92400e;margin-bottom:20px;">
                ⚠️ Dokumen akan divalidasi oleh Kepala Sekolah sebelum dinyatakan sah.
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Upload Dokumen</button>
                <a href="{{ route('rpp.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
