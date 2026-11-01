@extends('layouts.app')
@section('title','Evaluasi Kinerja')
@section('page-title','Evaluasi Kinerja')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Evaluasi Kinerja Guru</h1>
        <p>Penilaian kinerja dan performa guru per semester</p>
    </div>
    @if(auth()->user()->isKepalaSekolah())
    <a href="{{ route('evaluasi-kinerja.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Buat Evaluasi
    </a>
    @endif
</div>

<div class="card">
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
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;">
                                {{ strtoupper(substr($e->guru->user->name ?? '-', 0, 2)) }}
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
