@extends('layouts.app')
@section('title','Detail SK Tugas')
@section('page-title','Detail SK Tugas')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Detail SK Tugas</h1></div>
    <div style="display:flex;gap:8px;">
        @if($skTugas->file_path)
        <a href="{{ asset('storage/'.$skTugas->file_path) }}" target="_blank" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Download PDF
        </a>
        @endif
        <a href="{{ route('sk-tugas.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>
<div style="max-width:700px;">
    <div class="card">
        <div style="text-align:center;padding:20px 0 24px;border-bottom:1px solid var(--border);margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:8px;">SURAT KEPUTUSAN</div>
            <div style="font-size:22px;font-weight:800;color:var(--text-primary);margin-bottom:6px;">{{ $skTugas->judul_sk }}</div>
            <div style="font-family:monospace;font-size:14px;color:var(--primary);font-weight:600;">{{ $skTugas->no_sk }}</div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
            @foreach([
                ['Guru', $skTugas->guru?->user?->name ?? '-'],
                ['Jenjang', $skTugas->guru?->jenjang ?? '-'],
                ['Jenis SK', $skTugas->jenis_sk ?? '-'],
                ['Tanggal SK', $skTugas->tanggal_sk?->format('d/m/Y') ?? '-'],
                ['Berlaku Mulai', $skTugas->berlaku_mulai?->format('d/m/Y') ?? '-'],
                ['Berlaku Sampai', $skTugas->berlaku_sampai?->format('d/m/Y') ?? 'Tidak terbatas'],
                ['Diterbitkan Oleh', $skTugas->penerbit?->name ?? '-'],
                ['Tanggal Terbit', $skTugas->created_at?->format('d/m/Y H:i') ?? '-'],
            ] as [$label, $val])
            <div style="background:var(--body-bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:4px;">{{ $label }}</div>
                <div style="font-size:14px;font-weight:600;">{{ $val }}</div>
            </div>
            @endforeach
        </div>

        @if($skTugas->keterangan)
        <div style="background:var(--primary-50);border:1px solid var(--primary-200);border-radius:var(--radius-sm);padding:14px 16px;">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--primary-dark);margin-bottom:6px;">Keterangan</div>
            <div style="font-size:14px;line-height:1.6;">{{ $skTugas->keterangan }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
