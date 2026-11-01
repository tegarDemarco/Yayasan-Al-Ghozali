@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Pengurus Yayasan Dashboard --}}
@if($user->isPengurusYayasan())

<div class="welcome-banner">
    <h2>Selamat datang, {{ explode(' ', $user->name)[0] }} 👋</h2>
    <p>Pantau seluruh aktivitas guru di 3 jenjang sekolah Yayasan Al-Ghozaly</p>
</div>

{{-- Stats Row --}}
<div class="stats-grid">
    <div class="stat-card accent-blue">
        <div class="stat-icon icon-blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">3</div>
            <div class="stat-label">Sekolah (SD, MTs, SMK)</div>
        </div>
    </div>

    <div class="stat-card accent-green">
        <div class="stat-icon icon-green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['totalGuru'] ?? 0 }}</div>
            <div class="stat-label">Total Guru Aktif</div>
        </div>
    </div>

    <div class="stat-card accent-yellow">
        <div class="stat-icon icon-yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['persentaseKehadiran'] ?? 0 }}%</div>
            <div class="stat-label">Kehadiran Guru Hari Ini</div>
        </div>
    </div>

    <div class="stat-card accent-purple">
        <div class="stat-icon icon-purple">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($data['rataKinerja'] ?? 0, 1) }}</div>
            <div class="stat-label">Rata-rata Kinerja</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;margin-bottom:20px;">
    {{-- Attendance Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Rekap Kehadiran Guru</div>
                <div class="card-subtitle">7 hari terakhir – semua sekolah</div>
            </div>
        </div>
        <div class="chart-container" style="height:220px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Aktivitas Terbaru</div>
            <a href="{{ route('guru.index') }}" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Lihat Semua →</a>
        </div>
        @forelse($data['aktivitasTerbaru'] ?? [] as $aktivitas)
        <div class="activity-item">
            <div class="activity-dot {{ $aktivitas['warna'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="activity-text">
                <p>{{ $aktivitas['pesan'] }}</p>
                <div class="activity-time">{{ \Carbon\Carbon::parse($aktivitas['waktu'])->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:24px;">
            <p>Belum ada aktivitas terbaru</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Schools Grid --}}
<div class="card-header" style="margin-bottom:16px;">
    <h3 style="font-size:15px;font-weight:700;">Monitoring Sekolah</h3>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
    @foreach($data['sekolahs'] ?? [] as $sekolah)
    <div class="card" style="border-top:3px solid {{ $sekolah->jenjang === 'SD' ? '#3b82f6' : ($sekolah->jenjang === 'MTs' ? '#f59e0b' : '#ec4899') }};">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <span class="jenjang-{{ strtolower($sekolah->jenjang) }}">{{ $sekolah->jenjang }}</span>
            <span class="badge badge-green">Aktif</span>
        </div>
        <div style="font-size:15px;font-weight:700;margin-bottom:4px;">{{ $sekolah->nama_sekolah }}</div>
        <div style="font-size:12.5px;color:var(--text-muted);margin-bottom:16px;">{{ $sekolah->kepala_sekolah }}</div>
        <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);">
            <div style="text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--primary);">{{ $sekolah->gurus_count }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Guru Aktif</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--blue);">{{ $sekolah->persentaseKehadiran() }}%</div>
                <div style="font-size:11px;color:var(--text-muted);">Kehadiran</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--yellow);">{{ $sekolah->akreditasi ?? '-' }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Akreditasi</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@elseif($user->isKepalaSekolah())
{{-- Kepala Sekolah Dashboard --}}
<div class="welcome-banner">
    <h2>Selamat datang, {{ explode(' ', $user->name)[0] }} 👋</h2>
    <p>Dashboard Kepala Sekolah – {{ $data['sekolah']?->nama_sekolah ?? '' }}</p>
</div>

<div class="stats-grid">
    <div class="stat-card accent-green">
        <div class="stat-icon icon-green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['guruAktif'] ?? 0 }}</div>
            <div class="stat-label">Guru Aktif</div>
        </div>
    </div>
    <div class="stat-card accent-blue">
        <div class="stat-icon icon-blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ count($data['presensiHariIni'] ?? []) }}</div>
            <div class="stat-label">Presensi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card accent-yellow">
        <div class="stat-icon icon-yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['rppMenunggu'] ?? 0 }}</div>
            <div class="stat-label">RPP Menunggu Validasi</div>
        </div>
    </div>
    <div class="stat-card accent-red">
        <div class="stat-icon icon-red">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['izinMenunggu'] ?? 0 }}</div>
            <div class="stat-label">Izin Menunggu Approval</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><div class="card-title">Kehadiran Minggu Ini</div></div>
        <div class="chart-container" style="height:220px;"><canvas id="attendanceChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Presensi Hari Ini</div>
            <a href="{{ route('presensi.index') }}" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Lihat Semua →</a>
        </div>
        @php
            $hadir = collect($data['presensiHariIni'] ?? [])->where('status','hadir')->count();
            $tidakHadir = collect($data['presensiHariIni'] ?? [])->where('status','!=','hadir')->count();
        @endphp
        <div style="display:flex;gap:16px;margin-bottom:16px;">
            <div style="flex:1;background:var(--primary-50);border:1px solid var(--primary-200);border-radius:10px;padding:16px;text-align:center;">
                <div style="font-size:28px;font-weight:800;color:var(--primary);">{{ $hadir }}</div>
                <div style="font-size:12px;color:var(--primary-dark);font-weight:600;">Hadir</div>
            </div>
            <div style="flex:1;background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:16px;text-align:center;">
                <div style="font-size:28px;font-weight:800;color:var(--red);">{{ $tidakHadir }}</div>
                <div style="font-size:12px;color:#b91c1c;font-weight:600;">Tidak Hadir</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('rpp.index') }}?status=menunggu" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                Validasi RPP ({{ $data['rppMenunggu'] ?? 0 }})
            </a>
            <a href="{{ route('pengajuan-izin.index') }}?status=menunggu" class="btn btn-warning btn-sm" style="flex:1;justify-content:center;">
                Approval Izin ({{ $data['izinMenunggu'] ?? 0 }})
            </a>
        </div>
    </div>
</div>

@elseif($user->isGuru())
{{-- Guru Dashboard --}}
<div class="welcome-banner">
    <h2>Selamat datang, {{ explode(' ', $user->name)[0] }} 👋</h2>
    <p>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }} – {{ $data['guru']?->sekolah?->nama_sekolah ?? '' }}</p>
</div>

<div class="stats-grid">
    <div class="stat-card accent-blue">
        <div class="stat-icon icon-blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ count($data['jadwalHariIni'] ?? []) }}</div>
            <div class="stat-label">Jadwal Hari Ini</div>
        </div>
    </div>
    <div class="stat-card accent-green">
        <div class="stat-icon icon-green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['presensiHariIni'] ? ucfirst($data['presensiHariIni']->status) : 'Belum' }}</div>
            <div class="stat-label">Presensi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card accent-yellow">
        <div class="stat-icon icon-yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['jurnalBulanIni'] ?? 0 }}</div>
            <div class="stat-label">Jurnal Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card accent-purple">
        <div class="stat-icon icon-purple">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['pengajuanMenunggu'] ?? 0 }}</div>
            <div class="stat-label">Izin Menunggu</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Jadwal Hari Ini --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Jadwal Mengajar Hari Ini</div>
                <div class="card-subtitle">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM Y') }}</div>
            </div>
            <a href="{{ route('jadwal-mengajar.index') }}" style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Lihat Semua →</a>
        </div>
        @forelse($data['jadwalHariIni'] ?? [] as $jadwal)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
            <div style="width:56px;text-align:center;background:var(--primary-50);border:1px solid var(--primary-200);border-radius:8px;padding:6px 4px;">
                <div style="font-size:11px;font-weight:700;color:var(--primary);">{{ substr($jadwal->jam_mulai, 0, 5) }}</div>
                <div style="font-size:9.5px;color:var(--text-muted);">{{ substr($jadwal->jam_selesai, 0, 5) }}</div>
            </div>
            <div style="flex:1;">
                <div style="font-size:13.5px;font-weight:600;">{{ $jadwal->mata_pelajaran }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $jadwal->kelas }} @if($jadwal->ruangan) · {{ $jadwal->ruangan }} @endif</div>
            </div>
            <span class="badge badge-green">Aktif</span>
        </div>
        @empty
        <div class="empty-state" style="padding:24px 0;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px;height:40px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            <p>Tidak ada jadwal mengajar hari ini</p>
        </div>
        @endforelse
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Aksi Cepat</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <a href="{{ route('presensi.create') }}" class="btn btn-primary" style="justify-content:center;flex-direction:column;height:72px;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Isi Presensi
            </a>
            <a href="{{ route('jurnal.create') }}" class="btn btn-secondary" style="justify-content:center;flex-direction:column;height:72px;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                Isi Jurnal
            </a>
            <a href="{{ route('rpp.create') }}" class="btn btn-secondary" style="justify-content:center;flex-direction:column;height:72px;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                Upload RPP
            </a>
            <a href="{{ route('pengajuan-izin.create') }}" class="btn btn-warning" style="justify-content:center;flex-direction:column;height:72px;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                Ajukan Izin
            </a>
        </div>
    </div>
</div>

@else
{{-- Admin Sekolah Dashboard --}}
<div class="welcome-banner">
    <h2>Selamat datang, {{ explode(' ', $user->name)[0] }} 👋</h2>
    <p>Panel Admin – {{ $data['sekolah']?->nama_sekolah ?? '' }}</p>
</div>

<div class="stats-grid">
    <div class="stat-card accent-green">
        <div class="stat-icon icon-green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['guruAktif'] ?? 0 }}</div>
            <div class="stat-label">Guru Aktif</div>
        </div>
    </div>
    <div class="stat-card accent-blue">
        <div class="stat-icon icon-blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ count($data['presensiHariIni'] ?? []) }}</div>
            <div class="stat-label">Presensi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card accent-red">
        <div class="stat-icon icon-red">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $data['izinMenunggu'] ?? 0 }}</div>
            <div class="stat-label">Izin Menunggu</div>
        </div>
    </div>
</div>

<div style="display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ route('guru.index') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        Kelola Data Guru
    </a>
    <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
        Kelola Jadwal
    </a>
    <a href="{{ route('presensi.index') }}" class="btn btn-secondary">Rekap Presensi</a>
    <a href="{{ route('pengajuan-izin.index') }}" class="btn btn-warning">
        Izin Menunggu ({{ $data['izinMenunggu'] ?? 0 }})
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
@if(isset($data['kehadiranPerMinggu']))
const attendanceData = @json($data['kehadiranPerMinggu'] ?? []);
if (document.getElementById('attendanceChart') && attendanceData.length) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: attendanceData.map(d => d.hari),
            datasets: [{
                label: 'Hadir',
                data: attendanceData.map(d => d.persentase),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#16a34a',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 0, max: 100,
                    ticks: { callback: v => v + '%', font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
}
@endif
</script>
@endpush
