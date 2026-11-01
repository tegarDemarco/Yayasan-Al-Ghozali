@extends('layouts.app')
@section('title','Upload Bahan Ajar')
@section('page-title','Upload Bahan Ajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Upload Bahan Ajar</h1></div>
    <a href="{{ route('bahan-ajar.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:600px;">
    <div class="card">
        <form method="POST" action="{{ route('bahan-ajar.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul Bahan Ajar <span class="req">*</span></label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul') }}" placeholder="Nama bahan ajar" required>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Mis: X TKJ" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat bahan ajar...">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">File <span class="req">*</span> <span style="color:var(--text-muted);font-weight:400;">(PDF, DOC, PPT, JPG, PNG, ZIP – max 20MB)</span></label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.png,.zip" required>
                @error('file')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Upload Bahan Ajar</button>
                <a href="{{ route('bahan-ajar.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
