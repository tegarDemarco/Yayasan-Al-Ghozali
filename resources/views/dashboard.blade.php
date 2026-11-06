@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Beranda')

@section('content')
@php
    $user = auth()->user();
    $role = $user->role;
    $jenjang = $user->jenjang ?? 'SD';
    $currentDate = strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y'));
@endphp

{{-- ========================================================= --}}
{{-- 1. PENGURUS YAYASAN DASHBOARD --}}
{{-- ========================================================= --}}
@if($user->isPengurusYayasan())

    <!-- Page Eyebrow & Header -->
    <div class="page-header-block">
        <div>
            <div class="page-eyebrow">Manajemen Yayasan</div>
            <h1 class="page-main-title">Beranda</h1>
            <div class="page-sub-caption">Ringkasan aktivitas akademik dari seluruh satuan pendidikan.</div>
        </div>
    </div>

    <!-- Hero Banner Card -->
    <div class="hero-banner-card">
        <div class="hero-banner-content">
            <div class="hero-date-tag">{{ $currentDate }}</div>
            <div class="hero-headline">Selamat datang, Pengurus Yayasan</div>
            <div class="hero-description">Pantau tata kelola akademik SD, MTs, dan SMK dalam satu ruang.</div>
        </div>
        <div>
            <span class="hero-badge-pill">
                <i data-lucide="plus" style="width:13px;height:13px;"></i>
                <span>Tahun Ajaran 2026 / 2027</span>
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="stats-four-grid">
        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Satuan pendidikan</span>
                <div class="stat-icon-square"><i data-lucide="school"></i></div>
            </div>
            <div>
                <div class="stat-value-big">3</div>
                <div class="stat-subtext-detail">SD · MTs · SMK</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Total guru</span>
                <div class="stat-icon-square"><i data-lucide="users"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['totalGuru'] ?? 86 }}</div>
                <div class="stat-subtext-detail">Seluruh unit pendidikan</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Beban menunggu</span>
                <div class="stat-icon-square"><i data-lucide="clock"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['bebanMenunggu'] ?? 4 }}</div>
                <div class="stat-subtext-detail">Perlu verifikasi</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">SK diterbitkan</span>
                <div class="stat-icon-square"><i data-lucide="file-check"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['skDiterbitkan'] ?? 28 }}</div>
                <div class="stat-subtext-detail">Tahun ajaran 2026/2027</div>
            </div>
        </div>
    </div>

    <!-- 4 Quick Action Cards -->
    <div class="action-four-grid">
        <a href="{{ route('sk-tugas.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="file-text"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Terbitkan SK</span>
                <span class="action-desc-text">Buat penugasan guru</span>
            </div>
        </a>

        <a href="{{ route('rekap-beban.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="table-properties"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Verifikasi Beban</span>
                <span class="action-desc-text">Tinjau rekap jam</span>
            </div>
        </a>

        <a href="{{ route('guru.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="users"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Data Guru</span>
                <span class="action-desc-text">Lihat lintas unit</span>
            </div>
        </a>

        <a href="{{ route('guru.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="layout-grid"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Pilih Unit</span>
                <span class="action-desc-text">Pantau sekolah</span>
            </div>
        </a>
    </div>

    <!-- Split Content: Table (Left) & Summary Cards (Right) -->
    <div class="dashboard-split-layout">
        <!-- Left Table: Aktivitas Lintas Sekolah -->
        <div class="surface-white-card">
            <div class="card-header-flex">
                <div>
                    <div class="card-section-title">Aktivitas Lintas Sekolah</div>
                    <div class="card-section-subtitle">Status ringkas pekerjaan yang memerlukan perhatian.</div>
                </div>
                <a href="{{ route('guru.index') }}" class="card-header-link">Lihat semua &rarr;</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="wireframe-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Unit / Pengguna</th>
                            <th style="width: 50%;">Aktivitas</th>
                            <th style="width: 25%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['aktivitasLintasSekolah'] ?? [] as $item)
                        <tr>
                            <td><strong>{{ $item['unit'] }}</strong></td>
                            <td>
                                <div style="font-weight:600;color:var(--text-heading);">{{ $item['aktivitas'] }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $item['keterangan'] }}</div>
                            </td>
                            <td>
                                <span class="pill-status pill-status-{{ $item['status'] }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted);">Tidak ada aktivitas baru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Ringkasan Tindak Lanjut & Periode -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <!-- Card 1: Ringkasan Tindak Lanjut -->
            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan Tindak Lanjut</div>
                        <div class="card-section-subtitle">Informasi terbaru untuk unit Anda.</div>
                    </div>
                    <i data-lucide="more-horizontal" style="width:16px;height:16px;color:var(--text-subtle);"></i>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="sparkles"></i></div>
                    <div>
                        <div class="sidebar-item-title">Penguatan budaya mutu</div>
                        <div class="sidebar-item-desc">Pastikan jurnal mengajar dan dokumen perencanaan diperbarui secara berkala.</div>
                    </div>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="file-text"></i></div>
                    <div>
                        <div class="sidebar-item-title">Administrasi semester</div>
                        <div class="sidebar-item-desc">Periksa kembali jadwal, dokumen SK, dan kelengkapan arsip.</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Ringkasan Periode -->
            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan periode</div>
                        <div class="card-section-subtitle">Rekap operasional saat ini</div>
                    </div>
                </div>

                <div class="period-summary-row">
                    <span class="period-row-key">Periode aktif</span>
                    <span class="period-row-val">2026 / 2027</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Jenjang</span>
                    <span class="period-row-val">SD · MTs · SMK</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Terakhir diperbarui</span>
                    <span class="period-row-val">Hari ini, 09.15</span>
                </div>
            </div>
        </div>
    </div>


{{-- ========================================================= --}}
{{-- 2. KEPALA SEKOLAH DASHBOARD --}}
{{-- ========================================================= --}}
@elseif($user->isKepalaSekolah())

    <!-- Page Eyebrow & Header -->
    <div class="page-header-block">
        <div>
            <div class="page-eyebrow">{{ $jenjang }} · Kepala Sekolah</div>
            <h1 class="page-main-title">Beranda</h1>
            <div class="page-sub-caption">Berikut ringkasan aktivitas {{ $jenjang }} Al Ghozaly hari ini.</div>
        </div>
    </div>

    <!-- Hero Banner Card -->
    <div class="hero-banner-card">
        <div class="hero-banner-content">
            <div class="hero-date-tag">{{ $currentDate }}</div>
            <div class="hero-headline">Selamat datang, Kepala Sekolah</div>
            <div class="hero-description">Satu ruang untuk memantau, menindaklanjuti, dan menjaga mutu layanan pendidikan.</div>
        </div>
        <div>
            <span class="hero-badge-pill">
                <i data-lucide="plus" style="width:13px;height:13px;"></i>
                <span>Tahun Ajaran 2026 / 2027</span>
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="stats-four-grid">
        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Guru terdaftar</span>
                <div class="stat-icon-square"><i data-lucide="user"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['guruAktif'] ?? 28 }}</div>
                <div class="stat-subtext-detail">Pada unit {{ $jenjang }}</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Hadir hari ini</span>
                <div class="stat-icon-square"><i data-lucide="check"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['hadirHariIni'] ?? 25 }} / {{ $data['guruAktif'] ?? 28 }}</div>
                <div class="stat-subtext-detail">{{ $data['belumTercatat'] ?? 3 }} belum tercatat</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">RPP menunggu</span>
                <div class="stat-icon-square"><i data-lucide="folder"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['rppMenunggu'] ?? 2 }}</div>
                <div class="stat-subtext-detail">Perlu validasi</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Pengajuan izin</span>
                <div class="stat-icon-square"><i data-lucide="clock"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['izinMenunggu'] ?? 2 }}</div>
                <div class="stat-subtext-detail">Menunggu keputusan</div>
            </div>
        </div>
    </div>

    <!-- 4 Quick Action Cards -->
    <div class="action-four-grid">
        <a href="{{ route('rpp.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="folder-check"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Validasi RPP</span>
                <span class="action-desc-text">Tinjau dokumen</span>
            </div>
        </a>

        <a href="{{ route('pengajuan-izin.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="clock"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Putuskan Izin</span>
                <span class="action-desc-text">Tinjau pengajuan</span>
            </div>
        </a>

        <a href="{{ route('evaluasi-kinerja.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="sparkles"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Evaluasi Guru</span>
                <span class="action-desc-text">Isi penilaian</span>
            </div>
        </a>

        <a href="{{ route('jadwal-mengajar.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="calendar"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Lihat Jadwal</span>
                <span class="action-desc-text">Pantau jadwal</span>
            </div>
        </a>
    </div>

    <!-- Split Content: Table (Left) & Summary Cards (Right) -->
    <div class="dashboard-split-layout">
        <!-- Left Table: Perlu Perhatian -->
        <div class="surface-white-card">
            <div class="card-header-flex">
                <div>
                    <div class="card-section-title">Perlu Perhatian</div>
                    <div class="card-section-subtitle">Status ringkas pekerjaan yang memerlukan perhatian.</div>
                </div>
                <a href="{{ route('rpp.index') }}" class="card-header-link">Lihat semua &rarr;</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="wireframe-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Unit / Pengguna</th>
                            <th style="width: 50%;">Aktivitas</th>
                            <th style="width: 25%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['perluPerhatian'] ?? [] as $item)
                        <tr>
                            <td><strong>{{ $item['pengguna'] }}</strong></td>
                            <td>
                                <div style="font-weight:600;color:var(--text-heading);">{{ $item['aktivitas'] }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $item['keterangan'] }}</div>
                            </td>
                            <td>
                                <span class="pill-status pill-status-{{ $item['status'] }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted);">Tidak ada pekerjaan yang menunggu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan Tindak Lanjut</div>
                        <div class="card-section-subtitle">Informasi terbaru untuk unit Anda.</div>
                    </div>
                    <i data-lucide="more-horizontal" style="width:16px;height:16px;color:var(--text-subtle);"></i>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="sparkles"></i></div>
                    <div>
                        <div class="sidebar-item-title">Penguatan budaya mutu</div>
                        <div class="sidebar-item-desc">Pastikan jurnal mengajar dan dokumen perencanaan diperbarui secara berkala.</div>
                    </div>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="file-text"></i></div>
                    <div>
                        <div class="sidebar-item-title">Administrasi semester</div>
                        <div class="sidebar-item-desc">Periksa kembali jadwal, dokumen SK, dan kelengkapan arsip.</div>
                    </div>
                </div>
            </div>

            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan periode</div>
                        <div class="card-section-subtitle">Rekap operasional saat ini</div>
                    </div>
                </div>

                <div class="period-summary-row">
                    <span class="period-row-key">Periode aktif</span>
                    <span class="period-row-val">2026 / 2027</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Jenjang</span>
                    <span class="period-row-val">{{ $jenjang }}</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Terakhir diperbarui</span>
                    <span class="period-row-val">Hari ini, 09.15</span>
                </div>
            </div>
        </div>
    </div>


{{-- ========================================================= --}}
{{-- 3. ADMIN SEKOLAH DASHBOARD --}}
{{-- ========================================================= --}}
@elseif($user->isAdminSekolah())

    <!-- Page Eyebrow & Header -->
    <div class="page-header-block">
        <div>
            <div class="page-eyebrow">{{ $jenjang }} · Admin Sekolah</div>
            <h1 class="page-main-title">Beranda</h1>
            <div class="page-sub-caption">Berikut ringkasan aktivitas {{ $jenjang }} Al Ghozaly hari ini.</div>
        </div>
    </div>

    <!-- Hero Banner Card -->
    <div class="hero-banner-card">
        <div class="hero-banner-content">
            <div class="hero-date-tag">{{ $currentDate }}</div>
            <div class="hero-headline">Selamat datang, Admin Sekolah</div>
            <div class="hero-description">Satu ruang untuk memantau, menindaklanjuti, dan menjaga mutu layanan pendidikan.</div>
        </div>
        <div>
            <span class="hero-badge-pill">
                <i data-lucide="plus" style="width:13px;height:13px;"></i>
                <span>Tahun Ajaran 2026 / 2027</span>
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="stats-four-grid">
        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Guru terdaftar</span>
                <div class="stat-icon-square"><i data-lucide="user"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['guruAktif'] ?? 28 }}</div>
                <div class="stat-subtext-detail">Data aktif pada sekolah</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Jadwal terpublikasi</span>
                <div class="stat-icon-square"><i data-lucide="calendar"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['jadwalTerpublikasi'] ?? 34 }}</div>
                <div class="stat-subtext-detail">{{ $data['jadwalDraft'] ?? 2 }} jadwal masih draft</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Presensi hari ini</span>
                <div class="stat-icon-square"><i data-lucide="check"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['hadirHariIni'] ?? 25 }} / {{ $data['guruAktif'] ?? 28 }}</div>
                <div class="stat-subtext-detail">{{ $data['belumTercatat'] ?? 3 }} belum tercatat</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Arsip perlu ditinjau</span>
                <div class="stat-icon-square"><i data-lucide="folder"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['arsipPerluDitinjau'] ?? 3 }}</div>
                <div class="stat-subtext-detail">Dokumen belum lengkap</div>
            </div>
        </div>
    </div>

    <!-- 4 Quick Action Cards -->
    <div class="action-four-grid">
        <a href="{{ route('guru.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="user-plus"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Tambah Guru</span>
                <span class="action-desc-text">Kelola data guru</span>
            </div>
        </a>

        <a href="{{ route('jadwal-mengajar.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="calendar"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Susun Jadwal</span>
                <span class="action-desc-text">Atur jam mengajar</span>
            </div>
        </a>

        <a href="{{ route('sk-tugas.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="file-text"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Arsip SK</span>
                <span class="action-desc-text">Unggah dokumen SK</span>
            </div>
        </a>

        <a href="{{ route('rpp.index') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="folder"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Arsip RPP</span>
                <span class="action-desc-text">Kelola dokumen valid</span>
            </div>
        </a>
    </div>

    <!-- Split Content: Table (Left) & Summary Cards (Right) -->
    <div class="dashboard-split-layout">
        <!-- Left Table: Aktivitas Administrasi -->
        <div class="surface-white-card">
            <div class="card-header-flex">
                <div>
                    <div class="card-section-title">Aktivitas Administrasi</div>
                    <div class="card-section-subtitle">Status ringkas pekerjaan yang memerlukan perhatian.</div>
                </div>
                <a href="{{ route('jadwal-mengajar.index') }}" class="card-header-link">Lihat semua &rarr;</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="wireframe-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Unit / Pengguna</th>
                            <th style="width: 50%;">Aktivitas</th>
                            <th style="width: 25%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['aktivitasAdministrasi'] ?? [] as $item)
                        <tr>
                            <td><strong>{{ $item['unit_pengguna'] }}</strong></td>
                            <td>
                                <div style="font-weight:600;color:var(--text-heading);">{{ $item['aktivitas'] }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $item['keterangan'] }}</div>
                            </td>
                            <td>
                                <span class="pill-status pill-status-{{ $item['status'] }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted);">Tidak ada aktivitas administrasi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan Tindak Lanjut</div>
                        <div class="card-section-subtitle">Informasi terbaru untuk unit Anda.</div>
                    </div>
                    <i data-lucide="more-horizontal" style="width:16px;height:16px;color:var(--text-subtle);"></i>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="sparkles"></i></div>
                    <div>
                        <div class="sidebar-item-title">Penguatan budaya mutu</div>
                        <div class="sidebar-item-desc">Pastikan jurnal mengajar dan dokumen perencanaan diperbarui secara berkala.</div>
                    </div>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="file-text"></i></div>
                    <div>
                        <div class="sidebar-item-title">Administrasi semester</div>
                        <div class="sidebar-item-desc">Periksa kembali jadwal, dokumen SK, dan kelengkapan arsip.</div>
                    </div>
                </div>
            </div>

            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan periode</div>
                        <div class="card-section-subtitle">Rekap operasional saat ini</div>
                    </div>
                </div>

                <div class="period-summary-row">
                    <span class="period-row-key">Periode aktif</span>
                    <span class="period-row-val">2026 / 2027</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Jenjang</span>
                    <span class="period-row-val">{{ $jenjang }}</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Terakhir diperbarui</span>
                    <span class="period-row-val">Hari ini, 09.15</span>
                </div>
            </div>
        </div>
    </div>


{{-- ========================================================= --}}
{{-- 4. GURU DASHBOARD --}}
{{-- ========================================================= --}}
@else

    <!-- Page Eyebrow & Header -->
    <div class="page-header-block">
        <div>
            <div class="page-eyebrow">{{ $jenjang }} · Guru</div>
            <h1 class="page-main-title">Beranda</h1>
            <div class="page-sub-caption">Berikut ringkasan aktivitas {{ $jenjang }} Al Ghozaly hari ini.</div>
        </div>
    </div>

    <!-- Hero Banner Card -->
    <div class="hero-banner-card">
        <div class="hero-banner-content">
            <div class="hero-date-tag">{{ $currentDate }}</div>
            <div class="hero-headline">Selamat datang, {{ explode(' ', $user->name)[0] }}</div>
            <div class="hero-description">Semoga kegiatan belajar hari ini berjalan lancar dan bermakna.</div>
        </div>
        <div>
            <span class="hero-badge-pill">
                <i data-lucide="plus" style="width:13px;height:13px;"></i>
                <span>Tahun Ajaran 2026 / 2027</span>
            </span>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="stats-four-grid">
        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Jadwal hari ini</span>
                <div class="stat-icon-square"><i data-lucide="calendar"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['totalSesi'] ?? 4 }} sesi</div>
                <div class="stat-subtext-detail">{{ $data['sesiBerikutnya'] ?? 2 }} sesi berikutnya</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Presensi bulan ini</span>
                <div class="stat-icon-square"><i data-lucide="check"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['presensiBulanIniHadir'] ?? 18 }} / {{ $data['totalHariKerja'] ?? 20 }}</div>
                <div class="stat-subtext-detail">{{ $data['kehadiranPersen'] ?? 90 }}% kehadiran</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Jurnal terisi</span>
                <div class="stat-icon-square"><i data-lucide="book-open"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['jurnalTerisiPersen'] ?? 92 }}%</div>
                <div class="stat-subtext-detail">Bulan berjalan</div>
            </div>
        </div>

        <div class="stat-white-card">
            <div class="stat-card-top-row">
                <span class="stat-label-text">Beban mengajar</span>
                <div class="stat-icon-square"><i data-lucide="clock"></i></div>
            </div>
            <div>
                <div class="stat-value-big">{{ $data['bebanMengajarJam'] ?? 24 }} jam</div>
                <div class="stat-subtext-detail">Status: sesuai</div>
            </div>
        </div>
    </div>

    <!-- 4 Quick Action Cards -->
    <div class="action-four-grid">
        <a href="{{ route('presensi.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="check"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Isi Presensi</span>
                <span class="action-desc-text">Catat kehadiran</span>
            </div>
        </a>

        <a href="{{ route('jurnal.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="book-open"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Isi Jurnal</span>
                <span class="action-desc-text">Catat pembelajaran</span>
            </div>
        </a>

        <a href="{{ route('rpp.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="upload-cloud"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Upload RPP</span>
                <span class="action-desc-text">Kirim untuk validasi</span>
            </div>
        </a>

        <a href="{{ route('pengajuan-izin.create') }}" class="action-white-card">
            <div class="action-icon-box"><i data-lucide="clock"></i></div>
            <div class="action-text-col">
                <span class="action-title-text">Ajukan Izin</span>
                <span class="action-desc-text">Kirim permohonan</span>
            </div>
        </a>
    </div>

    <!-- Split Content: Timeline (Left) & Summary Cards (Right) -->
    <div class="dashboard-split-layout">
        <!-- Left Column: Jadwal Mengajar Hari Ini Timeline -->
        <div class="surface-white-card">
            <div class="card-header-flex">
                <div>
                    <div class="card-section-title">Jadwal Mengajar Hari Ini</div>
                    <div class="card-section-subtitle">Agenda pembelajaran sesuai jadwal terpublikasi.</div>
                </div>
                <a href="{{ route('jadwal-mengajar.index') }}" class="card-header-link">Lihat semua &rarr;</a>
            </div>

            <div class="timeline-agenda-list">
                @php
                    $jadwalList = $data['jadwalHariIni'] ?? collect();
                @endphp

                @if($jadwalList->isNotEmpty())
                    @foreach($jadwalList as $j)
                    <div class="timeline-agenda-item">
                        <div class="timeline-time-badge">{{ substr($j->jam_mulai, 0, 5) }}</div>
                        <div class="timeline-content-col">
                            <span class="timeline-item-title">{{ $j->mata_pelajaran }} · {{ $j->kelas }}</span>
                            <span class="timeline-item-sub">{{ substr($j->jam_mulai, 0, 5) }}–{{ substr($j->jam_selesai, 0, 5) }} · {{ $j->ruangan ?? 'Ruang Kelas' }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    @foreach($data['timelineSample'] ?? [] as $sample)
                    <div class="timeline-agenda-item">
                        <div class="timeline-time-badge">{{ $sample['jam'] }}</div>
                        <div class="timeline-content-col">
                            <span class="timeline-item-title">{{ $sample['mapel'] }} · {{ $sample['kelas'] }}</span>
                            <span class="timeline-item-sub">{{ $sample['waktu'] }} · {{ $sample['ruang'] }}</span>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Pengumuman</div>
                        <div class="card-section-subtitle">Informasi terbaru untuk unit Anda.</div>
                    </div>
                    <i data-lucide="more-horizontal" style="width:16px;height:16px;color:var(--text-subtle);"></i>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="sparkles"></i></div>
                    <div>
                        <div class="sidebar-item-title">Penguatan budaya mutu</div>
                        <div class="sidebar-item-desc">Pastikan jurnal mengajar dan dokumen perencanaan diperbarui secara berkala.</div>
                    </div>
                </div>

                <div class="sidebar-item-box">
                    <div class="sidebar-item-icon-square"><i data-lucide="file-text"></i></div>
                    <div>
                        <div class="sidebar-item-title">Administrasi semester</div>
                        <div class="sidebar-item-desc">Periksa kembali jadwal, dokumen SK, dan kelengkapan arsip.</div>
                    </div>
                </div>
            </div>

            <div class="surface-white-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-section-title">Ringkasan periode</div>
                        <div class="card-section-subtitle">Rekap operasional saat ini</div>
                    </div>
                </div>

                <div class="period-summary-row">
                    <span class="period-row-key">Periode aktif</span>
                    <span class="period-row-val">2026 / 2027</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Jenjang</span>
                    <span class="period-row-val">{{ $jenjang }}</span>
                </div>
                <div class="period-summary-row">
                    <span class="period-row-key">Terakhir diperbarui</span>
                    <span class="period-row-val">Hari ini, 09.15</span>
                </div>
            </div>
        </div>
    </div>

@endif

@endsection
