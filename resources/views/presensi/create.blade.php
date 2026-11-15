@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role;
    $jenjang = $user->jenjang ?? 'SD';

    $roleTitles = [
        'pengurus_yayasan' => 'Pengurus Yayasan',
        'kepala_sekolah' => 'Kepala Sekolah',
        'admin_sekolah' => 'Admin Sekolah',
        'guru' => 'Guru',
    ];
    $roleTitle = $roleTitles[$role] ?? 'Pengguna';
@endphp

@section('title', 'Isi Presensi')
@section('page-title', 'Isi Presensi')

@section('content')

<!-- Page Eyebrow & Header -->
<div class="page-header-block">
    <div>
        <div class="page-eyebrow">{{ $jenjang }} · {{ $roleTitle }}</div>
        <h1 class="page-main-title">{{ $user->isGuru() ? 'Isi Presensi Hari Ini' : 'Tambah Presensi Guru' }}</h1>
        <div class="page-sub-caption">Catat kehadiran harian pada sistem informasi akademik.</div>
    </div>
    <div>
        <a href="{{ route('presensi.index') }}" class="btn-pill-action btn-pill-secondary">
            &larr; Kembali ke Presensi
        </a>
    </div>
</div>

<div style="max-width: 600px;">
    <div class="surface-white-card">
        <form method="POST" action="{{ route('presensi.store') }}">
            @csrf

            @if(!$user->isGuru())
            <div class="form-group">
                <label class="form-label">Pilih Guru <span class="req">*</span></label>
                <select name="guru_id" class="form-control-select" style="width:100%;" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                        {{ $g->user->name }} ({{ $g->jenjang }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal <span class="req">*</span></label>
                    <input type="date" name="tanggal" class="form-control-input" value="{{ old('tanggal', date('Y-m-d')) }}" required style="padding-left:12px;">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Kehadiran <span class="req">*</span></label>
                    <select name="status" class="form-control-select" style="width:100%;" required>
                        @foreach(['hadir'=>'Hadir','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha','cuti'=>'Cuti'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status','hadir') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Waktu Masuk</label>
                    <input type="time" name="waktu_masuk" class="form-control-input" value="{{ old('waktu_masuk', date('H:i')) }}" style="padding-left:12px;">
                </div>

                <div class="form-group">
                    <label class="form-label">Waktu Keluar</label>
                    <input type="time" name="waktu_keluar" class="form-control-input" value="{{ old('waktu_keluar') }}" style="padding-left:12px;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control-input" rows="3" style="height:auto;padding:10px;"
                          placeholder="Keterangan kehadiran atau alasan jika izin/sakit...">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <a href="{{ route('presensi.index') }}" class="btn-pill-action btn-pill-secondary">Batal</a>
                <button type="submit" class="btn-forest-primary">Simpan Presensi</button>
            </div>
        </form>
    </div>
</div>

@endsection
