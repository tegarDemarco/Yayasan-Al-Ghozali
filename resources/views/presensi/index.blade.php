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

@section('title', 'Presensi Guru')
@section('page-title', 'Presensi Guru')

@section('content')

<!-- Page Eyebrow & Header -->
<div class="page-header-block">
    <div>
        <div class="page-eyebrow">{{ $jenjang }} · {{ $roleTitle }}</div>
        <h1 class="page-main-title">Presensi Guru</h1>
        <div class="page-sub-caption">Rekapitulasi kehadiran guru harian pada satuan pendidikan.</div>
    </div>
    @if(auth()->user()->isGuru())
    <div>
        <a href="{{ route('presensi.create') }}" class="btn-forest-primary">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            <span>Isi Presensi Hari Ini</span>
        </a>
    </div>
    @endif
</div>

<!-- Filter Bar -->
<div class="surface-white-card" style="padding:14px 20px; margin-bottom:18px;">
    <form method="GET" action="{{ route('presensi.index') }}" style="margin:0;">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <div style="font-size:13px;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:6px;">
                <i data-lucide="calendar" style="width:16px;height:16px;color:var(--forest-600);"></i>
                <span>Pilih Tanggal:</span>
            </div>
            <div style="max-width:200px;">
                <input type="date" name="tanggal" class="form-control-input" value="{{ $tanggal }}" onchange="this.form.submit()" style="padding-left:12px;">
            </div>
            <button type="submit" class="btn-forest-primary" style="padding:7px 16px;font-size:13px;">
                Tampilkan
            </button>
            @if($tanggal !== date('Y-m-d'))
            <a href="{{ route('presensi.index') }}" class="btn-pill-action btn-pill-secondary">
                Hari Ini
            </a>
            @endif
        </div>
    </form>
</div>

<!-- 4 Stats Cards (Hadir, Izin, Sakit, Alpha) -->
<div class="stats-four-grid">
    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Hadir</span>
            <div class="stat-icon-square" style="background:#dcfce7;color:#15803d;"><i data-lucide="check" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasan['hadir'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Tercatat hadir tepat waktu</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Izin</span>
            <div class="stat-icon-square" style="background:#e0f2fe;color:#0284c7;"><i data-lucide="clock" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasan['izin'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Izin dengan pemberitahuan</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Sakit</span>
            <div class="stat-icon-square" style="background:#fef3c7;color:#b45309;"><i data-lucide="activity" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasan['sakit'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Dengan surat keterangan</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Alpha</span>
            <div class="stat-icon-square" style="background:#fee2e2;color:#dc2626;"><i data-lucide="x-circle" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasan['alpha'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Tanpa keterangan</div>
        </div>
    </div>
</div>

<!-- Presensi Table Surface Card -->
<div class="surface-white-card">
    <div class="card-header-flex">
        <div>
            <div class="card-section-title">Data Presensi – {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
            <div class="card-section-subtitle">Daftar presensi guru yang tercatat pada sistem.</div>
        </div>
        <span class="filter-data-count">{{ $presensi->total() }} data presensi</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="wireframe-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    @if(!auth()->user()->isGuru())
                    <th style="width: 25%;">Nama Guru</th>
                    <th style="width: 10%;">Jenjang</th>
                    @endif
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 12%;">Waktu Masuk</th>
                    <th style="width: 20%;">Keterangan</th>
                    <th style="width: 10%; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensi as $p)
                @php
                    $statusStyles = [
                        'hadir' => 'pill-status-selesai',
                        'izin' => 'pill-status-info',
                        'sakit' => 'pill-status-menunggu',
                        'alpha' => 'pill-status-revisi',
                        'cuti' => 'pill-status-menunggu',
                    ];
                    $badgeCls = $statusStyles[strtolower($p->status)] ?? 'pill-status-info';
                @endphp
                <tr>
                    <td>{{ $presensi->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())
                    <td>
                        <strong style="color:var(--text-heading);">{{ $p->guru->user->name ?? '-' }}</strong>
                        <div style="font-size:11px;color:var(--text-subtle);">{{ $p->guru?->nip ?? 'NIP -' }}</div>
                    </td>
                    <td>
                        <span class="jenjang-{{ strtolower($p->guru->jenjang ?? '') }}">{{ $p->guru->jenjang ?? '-' }}</span>
                    </td>
                    @endif
                    <td style="color:var(--text-muted);font-size:12.5px;">{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <span class="pill-status {{ $badgeCls }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--text-heading);font-weight:600;">
                        {{ $p->waktu_masuk ?? '-' }}
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);">
                        {{ $p->keterangan ? \Str::limit($p->keterangan, 35) : '-' }}
                    </td>
                    <td style="text-align: right;">
                        @if(auth()->user()->isGuru())
                        <form method="POST" action="{{ route('presensi.destroy', $p) }}" onsubmit="return confirm('Hapus data presensi ini?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-pill-action btn-pill-danger" style="padding:3px 10px;font-size:11.5px;">
                                Hapus
                            </button>
                        </form>
                        @else
                        <span class="btn-pill-action btn-pill-secondary" style="opacity:0.75;padding:3px 10px;font-size:11px;">
                            Tercatat
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ !auth()->user()->isGuru() ? 8 : 6 }}" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i data-lucide="calendar-x" style="width:36px;height:36px;color:var(--text-subtle);margin:0 auto 10px;display:block;"></i>
                        <div style="font-weight:700;font-size:14px;color:var(--text-heading);margin-bottom:4px;">Belum ada data presensi</div>
                        <div style="font-size:12.5px;">Tidak ditemukan data presensi pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($presensi->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 12px; color: var(--text-muted);">
            Menampilkan {{ $presensi->firstItem() }}–{{ $presensi->lastItem() }} dari {{ $presensi->total() }} data
        </span>
        <div>
            {{ $presensi->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Prototype Footnote -->
<div class="footnote-prototype">
    Data pada prototipe ini bersifat ilustratif, bukan data operasional.
</div>

@endsection
