@extends('layouts.app')
@section('title','Evaluasi Kinerja')
@section('page-title','Evaluasi Kinerja')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Evaluasi Kinerja Guru</h1>
        <p>Penilaian kinerja dan performa guru per semester</p>
    </div>
    @if(auth()->user()->isKepalaSekolah() || auth()->user()->isPengurusYayasan())
    <a href="{{ route('evaluasi-kinerja.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Buat Evaluasi
    </a>
    @endif
</div>

<div class="card">
    <form method="GET" action="{{ route('evaluasi-kinerja.index') }}" autocomplete="off">
        <div class="filter-bar">
            @if(auth()->user()->isPengurusYayasan())
            <select name="jenjang" class="form-control" style="width:auto;">
                <option value="">Semua Jenjang</option>
                <option value="SD" {{ request('jenjang') === 'SD' ? 'selected' : '' }}>Unit SD</option>
                <option value="MTs" {{ request('jenjang') === 'MTs' ? 'selected' : '' }}>Unit MTs</option>
                <option value="SMK" {{ request('jenjang') === 'SMK' ? 'selected' : '' }}>Unit SMK</option>
            </select>
            @endif
            <select name="predikat" class="form-control" style="width:auto;">
                <option value="">Semua Predikat</option>
                @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $p)
                <option value="{{ $p }}" {{ request('predikat') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <div class="search-box" style="max-width:260px;">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" class="form-control" placeholder="Cari nama guru..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                Filter
            </button>
            @if(request()->anyFilled(['jenjang','predikat','search']))
            <a href="{{ route('evaluasi-kinerja.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guru</th>
                    <th>Tahun Ajaran</th>
                    <th style="text-align:center;">Kinerja</th>
                    <th style="text-align:center;">Kehadiran</th>
                    <th style="text-align:center;">Administrasi</th>
                    <th style="text-align:center;">Pembelajaran</th>
                    <th style="text-align:center;">Nilai Akhir</th>
                    <th>Predikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $predikatColors = ['Sangat Baik'=>'badge-green','Baik'=>'badge-blue','Cukup'=>'badge-yellow','Kurang'=>'badge-red'];
                @endphp
                @forelse($evaluasis as $e)
                <tr>
                    <td>{{ $evaluasis->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;overflow:hidden;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                @if($e->guru?->user?->foto)
                                    <img src="{{ asset('storage/' . $e->guru->user->foto) }}" alt="{{ $e->guru->user->name }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.style.display='none';this.parentElement.textContent='{{ strtoupper(substr($e->guru->user->name ?? '-', 0, 2)) }}';">
                                @else
                                    {{ strtoupper(substr($e->guru->user->name ?? '-', 0, 2)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $e->guru->user->name ?? '-' }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $e->guru->spesialisasi ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $e->tahun_ajaran }} / {{ $e->semester }}</td>
                    @foreach(['nilai_kinerja','nilai_kehadiran','nilai_administrasi','nilai_pembelajaran'] as $field)
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:{{ $e->$field >= 75 ? 'var(--primary)' : 'var(--red)' }};">{{ number_format($e->$field, 0) }}</span>
                    </td>
                    @endforeach
                    <td style="text-align:center;">
                        <span style="font-size:18px;font-weight:800;color:{{ $e->nilai_akhir >= 75 ? 'var(--primary)' : 'var(--red)' }};">{{ number_format($e->nilai_akhir, 1) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $predikatColors[$e->predikat] ?? 'badge-gray' }}" style="font-size:12.5px;padding:5px 12px;">
                            {{ $e->predikat }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('evaluasi-kinerja.show', $e) }}" class="btn btn-secondary btn-sm btn-icon" title="Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        <h3>Belum ada data evaluasi kinerja</h3>
                        @if(auth()->user()->isKepalaSekolah())
                        <p>Klik "Buat Evaluasi" untuk menilai kinerja guru</p>
                        @endif
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Bobot Penilaian Info --}}
<div class="card" style="margin-top:16px;">
    <div class="card-title" style="margin-bottom:12px;">Bobot Komponen Penilaian</div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @foreach([
            ['Kinerja Umum','30%','icon-green'],
            ['Kehadiran','25%','icon-blue'],
            ['Administrasi','20%','icon-yellow'],
            ['Pembelajaran','25%','icon-purple'],
        ] as [$label, $pct, $cls])
        <div style="text-align:center;background:var(--body-bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;">
            <div style="font-size:22px;font-weight:800;color:var(--primary);">{{ $pct }}</div>
            <div style="font-size:12.5px;color:var(--text-muted);margin-top:4px;">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
