@extends('layouts.app')
@section('title','Isi Presensi')
@section('page-title','Isi Presensi')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>{{ auth()->user()->isGuru() ? 'Isi Presensi Hari Ini' : 'Tambah Presensi Guru' }}</h1></div>
    <a href="{{ route('presensi.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:560px;">
    <div class="card">
        <form method="POST" action="{{ route('presensi.store') }}">
            @csrf
            @if(!auth()->user()->isGuru())
            <div class="form-group">
                <label class="form-label">Guru <span class="req">*</span></label>
                <select name="guru_id" class="form-control" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->user->name }} ({{ $g->jenjang }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal <span class="req">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span class="req">*</span></label>
                    <select name="status" class="form-control" required>
                        @foreach(['hadir'=>'Hadir','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha','cuti'=>'Cuti'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status','hadir') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Waktu Masuk</label>
                    <input type="time" name="waktu_masuk" class="form-control" value="{{ old('waktu_masuk') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Waktu Keluar</label>
                    <input type="time" name="waktu_keluar" class="form-control" value="{{ old('waktu_keluar') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Presensi</button>
                <a href="{{ route('presensi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
