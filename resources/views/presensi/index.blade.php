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
        <div class="page-sub-caption">
            {{ ($tab ?? 'harian') === 'rekap' ? 'Rekapitulasi akumulasi kehadiran guru per bulan pada satuan pendidikan.' : 'Rekapitulasi kehadiran guru harian pada satuan pendidikan.' }}
        </div>
    </div>
    @if(auth()->user()->isGuru() && ($tab ?? 'harian') !== 'rekap')
    <div>
        <a href="{{ route('presensi.create') }}" class="btn-forest-primary">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            <span>Isi Presensi Hari Ini</span>
        </a>
    </div>
    @endif
</div>

<!-- Tabs Switcher: Harian vs Rekap Bulanan -->
<div class="nav-tabs-rekap" style="display:flex;align-items:center;gap:10px;margin-bottom:18px;border-bottom:1px solid var(--card-border,#e2e8f0);padding-bottom:12px;">
    <a href="{{ route('presensi.index', ['tab' => 'harian']) }}"
       class="btn-pill-action {{ ($tab ?? 'harian') !== 'rekap' ? 'btn-pill-primary' : 'btn-pill-secondary' }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:13px;text-decoration:none;font-weight:600;">
        <i data-lucide="calendar" style="width:15px;height:15px;"></i>
        <span>Presensi Harian</span>
    </a>
    <a href="{{ route('presensi.index', ['tab' => 'rekap']) }}"
       class="btn-pill-action {{ ($tab ?? 'harian') === 'rekap' ? 'btn-pill-primary' : 'btn-pill-secondary' }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:13px;text-decoration:none;font-weight:600;">
        <i data-lucide="bar-chart-2" style="width:15px;height:15px;"></i>
        <span>Rekapitulasi Bulanan</span>
    </a>
</div>

@if(($tab ?? 'harian') === 'rekap')
{{-- ========================================================= --}}
{{-- TAMPILAN REKAPITULASI BULANAN --}}
{{-- ========================================================= --}}

<!-- Filter Bar Rekap Bulanan -->
<div class="surface-white-card" style="padding:16px 20px; margin-bottom:18px;">
    <form method="GET" action="{{ route('presensi.index') }}" style="margin:0;">
        <input type="hidden" name="tab" value="rekap">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <div style="font-size:13px;font-weight:600;color:var(--text-heading);display:flex;align-items:center;gap:6px;">
                    <i data-lucide="filter" style="width:16px;height:16px;color:var(--forest-600);"></i>
                    <span>Periode Rekap:</span>
                </div>

                {{-- Pilih Bulan --}}
                <select name="bulan" class="form-control-select" style="min-width:140px;" onchange="this.form.submit()">
                    @foreach($namaBulan as $num => $nama)
                    <option value="{{ $num }}" {{ (int)$bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>

                {{-- Pilih Tahun --}}
                <select name="tahun" class="form-control-select" style="min-width:100px;" onchange="this.form.submit()">
                    @for($y = Carbon\Carbon::now()->year + 1; $y >= Carbon\Carbon::now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                @if(auth()->user()->isPengurusYayasan())
                <select name="jenjang" class="form-control-select" style="min-width:130px;" onchange="this.form.submit()">
                    <option value="">Semua Unit</option>
                    <option value="SD" {{ request('jenjang') === 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="MTs" {{ request('jenjang') === 'MTs' ? 'selected' : '' }}>MTs</option>
                    <option value="SMK" {{ request('jenjang') === 'SMK' ? 'selected' : '' }}>SMK</option>
                </select>
                @endif

                <button type="submit" class="btn-forest-primary" style="padding:7px 16px;font-size:13px;">
                    Tampilkan Rekap
                </button>
            </div>

            <div>
                <button type="button" onclick="window.print()" class="btn-pill-action btn-pill-secondary" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;font-size:13px;font-weight:600;">
                    <i data-lucide="printer" style="width:15px;height:15px;"></i>
                    <span>Cetak / Ekspor Rekap</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- 5 Stat Cards Rekap Bulanan -->
<div class="stats-four-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 20px;">
    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Total Guru Terdata</span>
            <div class="stat-icon-square" style="background:#f1f5f9;color:#475569;"><i data-lucide="users" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasanBulan['total_guru'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Data aktif unit sekolah</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Akumulasi Hadir (H)</span>
            <div class="stat-icon-square" style="background:#dcfce7;color:#15803d;"><i data-lucide="check" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasanBulan['hadir'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Presensi hadir tercatat</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Izin / Cuti (I/C)</span>
            <div class="stat-icon-square" style="background:#e0f2fe;color:#0284c7;"><i data-lucide="clock" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ ($ringkasanBulan['izin'] ?? 0) + ($ringkasanBulan['cuti'] ?? 0) }}</div>
            <div class="stat-subtext-detail">Dengan keterangan resmi</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Sakit (S)</span>
            <div class="stat-icon-square" style="background:#fef3c7;color:#b45309;"><i data-lucide="activity" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasanBulan['sakit'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Keterangan sakit</div>
        </div>
    </div>

    <div class="stat-white-card">
        <div class="stat-card-top-row">
            <span class="stat-label-text">Alpha (A)</span>
            <div class="stat-icon-square" style="background:#fee2e2;color:#dc2626;"><i data-lucide="x-circle" style="width:16px;height:16px;"></i></div>
        </div>
        <div>
            <div class="stat-value-big">{{ $ringkasanBulan['alpha'] ?? 0 }}</div>
            <div class="stat-subtext-detail">Tanpa keterangan</div>
        </div>
    </div>
</div>

<!-- Table Surface Card Rekap Bulanan -->
<div class="surface-white-card">
    <div class="card-header-flex">
        <div>
            <div class="card-section-title">Tabel Rekapitulasi Presensi – {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</div>
            <div class="card-section-subtitle">Rekap akumulasi kehadiran per guru selama 1 bulan kalender.</div>
        </div>
        <span class="filter-data-count">{{ count($rekapGuru) }} guru tercatat</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="wireframe-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Guru</th>
                    <th style="width: 12%;">Jenjang & Mapel</th>
                    <th style="width: 8%; text-align: center;">Hadir (H)</th>
                    <th style="width: 8%; text-align: center;">Izin (I)</th>
                    <th style="width: 8%; text-align: center;">Sakit (S)</th>
                    <th style="width: 8%; text-align: center;">Alpha (A)</th>
                    <th style="width: 8%; text-align: center;">Cuti (C)</th>
                    <th style="width: 10%; text-align: center;">Total Record</th>
                    <th style="width: 16%; text-align: right;">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapGuru as $index => $item)
                @php
                    $g = $item['guru'];
                    $persen = $item['persentase'];
                    $persenColor = $persen >= 85 ? '#16a34a' : ($persen >= 70 ? '#d97706' : '#dc2626');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong style="color:var(--text-heading);font-size:13.5px;">{{ $g->user->name ?? '-' }}</strong>
                        <div style="font-size:11px;color:var(--text-subtle);">NIP: {{ $g->nip ?? '-' }} · {{ $g->status_kepegawaian ?? 'GTY' }}</div>
                    </td>
                    <td>
                        <span class="jenjang-{{ strtolower($g->jenjang ?? '') }}">{{ $g->jenjang ?? '-' }}</span>
                        <div style="font-size:11px;color:var(--text-subtle);margin-top:2px;">{{ $g->spesialisasi ?? '-' }}</div>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill-status pill-status-selesai" style="font-weight:700;">{{ $item['hadir'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill-status pill-status-info">{{ $item['izin'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill-status pill-status-menunggu">{{ $item['sakit'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill-status pill-status-revisi" style="font-weight:{{ $item['alpha'] > 0 ? '700' : 'normal' }};">{{ $item['alpha'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill-status pill-status-menunggu">{{ $item['cuti'] }}</span>
                    </td>
                    <td style="text-align: center; font-weight: 600; color: var(--text-heading);">
                        {{ $item['total'] }} hari
                    </td>
                    <td style="text-align: right;">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                            <div style="width:70px;background:#e2e8f0;border-radius:999px;height:6px;overflow:hidden;">
                                <div style="width:{{ min(100, $persen) }}%;background:{{ $persenColor }};height:100%;"></div>
                            </div>
                            <span style="font-weight:700;font-size:12.5px;color:{{ $persenColor }};min-width:44px;">
                                {{ $persen }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i data-lucide="users" style="width:36px;height:36px;color:var(--text-subtle);margin:0 auto 10px;display:block;"></i>
                        <div style="font-weight:700;font-size:14px;color:var(--text-heading);margin-bottom:4px;">Tidak ada data guru</div>
                        <div style="font-size:12.5px;">Belum ada data guru terdaftar pada unit sekolah ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
{{-- ========================================================= --}}
{{-- TAMPILAN PRESENSI HARIAN (DEFAULT) --}}
{{-- ========================================================= --}}

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

    @if(isset($presensi) && $presensi->hasPages())
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
@endif

<!-- Prototype Footnote -->
<div class="footnote-prototype">
    Data pada prototipe ini bersifat ilustratif, bukan data operasional.
</div>

<style>
@media print {
    #appSidebar, .sidebar-overlay, .app-header, .nav-tabs-rekap, form, .footnote-prototype, .page-header-block a, button {
        display: none !important;
    }
    .app-main {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .surface-white-card, .card {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        margin-bottom: 12px !important;
    }
    body {
        background: #fff !important;
    }
}
</style>

@endsection
