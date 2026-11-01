@extends('layouts.app')
@section('title','Detail Evaluasi Kinerja')
@section('page-title','Detail Evaluasi Kinerja')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Detail Evaluasi Kinerja</h1></div>
    <a href="{{ route('evaluasi-kinerja.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:700px;">
    <div class="card" style="margin-bottom:16px;">
        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:16px;padding-bottom:20px;border-bottom:1px solid var(--border);margin-bottom:20px;">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:22px;">
                {{ strtoupper(substr($evaluasiKinerja->guru->user->name ?? '-', 0, 2)) }}
            </div>
            <div style="flex:1;">
                <div style="font-size:18px;font-weight:800;">{{ $evaluasiKinerja->guru->user->name ?? '-' }}</div>
                <div style="font-size:13px;color:var(--text-muted);">{{ $evaluasiKinerja->guru->spesialisasi ?? '' }} – {{ $evaluasiKinerja->sekolah->nama_sekolah ?? '' }}</div>
                <div style="margin-top:6px;">
                    <span class="badge {{ match($evaluasiKinerja->predikat) {
                        'Sangat Baik' => 'badge-green',
                        'Baik' => 'badge-blue',
                        'Cukup' => 'badge-yellow',
                        default => 'badge-red'
                    } }}" style="font-size:13px;padding:5px 14px;">
                        {{ $evaluasiKinerja->predikat }}
                    </span>
                </div>
            </div>
            <div style="text-align:center;background:var(--primary-50);border:2px solid var(--primary-200);border-radius:var(--radius);padding:16px 24px;">
                <div style="font-size:36px;font-weight:900;color:var(--primary);line-height:1;">{{ number_format($evaluasiKinerja->nilai_akhir, 1) }}</div>
                <div style="font-size:11px;color:var(--primary-dark);font-weight:600;margin-top:4px;">NILAI AKHIR</div>
            </div>
        </div>

        {{-- Scores --}}
        <div style="display:grid;gap:12px;margin-bottom:20px;">
            @foreach([
                ['nilai_kinerja','Kinerja Umum','30%','green'],
                ['nilai_kehadiran','Kehadiran','25%','blue'],
                ['nilai_administrasi','Administrasi','20%','yellow'],
                ['nilai_pembelajaran','Kualitas Pembelajaran','25%','purple'],
            ] as [$field, $label, $bobot, $color])
            @php $nilai = $evaluasiKinerja->$field; @endphp
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:150px;font-size:13px;color:var(--text-secondary);">{{ $label }}</div>
                <div style="flex:1;background:#f1f5f9;border-radius:100px;height:10px;overflow:hidden;">
                    <div style="height:100%;width:{{ $nilai }}%;background:{{ $color === 'green' ? '#16a34a' : ($color === 'blue' ? '#3b82f6' : ($color === 'yellow' ? '#f59e0b' : '#8b5cf6')) }};border-radius:100px;transition:width 1s ease;"></div>
                </div>
                <div style="width:48px;text-align:right;font-size:15px;font-weight:800;color:{{ $nilai >= 75 ? 'var(--primary)' : 'var(--red)' }};">{{ $nilai }}</div>
                <span class="badge badge-{{ $color }}" style="font-size:11px;">{{ $bobot }}</span>
            </div>
            @endforeach
        </div>

        {{-- Info --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
            @foreach([
                ['Tahun Ajaran', $evaluasiKinerja->tahun_ajaran . ' / ' . $evaluasiKinerja->semester],
                ['Dievaluasi Oleh', $evaluasiKinerja->evaluator->name ?? '-'],
                ['Tanggal Evaluasi', $evaluasiKinerja->dievaluasi_at?->format('d/m/Y H:i') ?? '-'],
            ] as [$label, $val])
            <div>
                <div style="font-size:11.5px;color:var(--text-muted);margin-bottom:3px;">{{ $label }}</div>
                <div style="font-size:13.5px;font-weight:600;">{{ $val }}</div>
            </div>
            @endforeach
        </div>

        @if($evaluasiKinerja->catatan)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:8px;">Catatan Evaluator</div>
            <div style="background:var(--accent-light);border:1px solid #fde68a;border-radius:var(--radius-sm);padding:14px;font-size:14px;line-height:1.7;">{{ $evaluasiKinerja->catatan }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
