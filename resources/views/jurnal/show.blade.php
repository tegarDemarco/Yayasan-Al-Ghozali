@extends('layouts.app')
@section('title','Detail Jurnal Mengajar')
@section('page-title','Detail Jurnal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Detail Jurnal Mengajar</h1></div>
    <a href="{{ route('jurnal.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:720px;">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;">
            <div>
                <div style="font-size:20px;font-weight:800;">{{ $jurnal->mata_pelajaran }}</div>
                <div style="font-size:14px;color:var(--text-muted);">{{ $jurnal->kelas }} – {{ $jurnal->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
            <span class="badge {{ $jurnal->status === 'terkirim' ? 'badge-green' : 'badge-yellow' }}" style="font-size:13px;padding:6px 14px;">{{ ucfirst($jurnal->status) }}</span>
        </div>

        @foreach([
            ['Guru', $jurnal->guru->user->name ?? '-'],
            ['Pertemuan Ke', $jurnal->pertemuan_ke ?? '-'],
            ['Jumlah Siswa Hadir', $jurnal->jumlah_siswa_hadir.' siswa'],
        ] as [$label, $val])
        <div style="display:flex;padding:10px 0;border-bottom:1px solid var(--border);font-size:13.5px;">
            <div style="width:180px;color:var(--text-muted);font-weight:500;">{{ $label }}</div>
            <div style="font-weight:600;">{{ $val }}</div>
        </div>
        @endforeach

        <div style="margin-top:20px;">
            <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Materi Pembelajaran</div>
            <div style="background:var(--primary-50);border-left:3px solid var(--primary);padding:14px 16px;border-radius:0 var(--radius-sm) var(--radius-sm) 0;font-size:14px;line-height:1.7;">{{ $jurnal->materi }}</div>
        </div>
        @if($jurnal->kegiatan)
        <div style="margin-top:16px;">
            <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Kegiatan Pembelajaran</div>
            <div style="background:#f8fafc;padding:14px 16px;border-radius:var(--radius-sm);font-size:14px;line-height:1.7;border:1px solid var(--border);">{{ $jurnal->kegiatan }}</div>
        </div>
        @endif
        @if($jurnal->catatan)
        <div style="margin-top:16px;">
            <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Catatan</div>
            <div style="background:var(--accent-light);padding:14px 16px;border-radius:var(--radius-sm);font-size:14px;line-height:1.7;border:1px solid #fde68a;">{{ $jurnal->catatan }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
