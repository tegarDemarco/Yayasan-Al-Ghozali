@extends('layouts.app')
@section('title','Rekap Beban Mengajar')
@section('page-title','Rekap Beban Mengajar')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

@section('content')
<div class="page-header-row" style="margin-bottom: 20px;">
    <div>
        <h1 class="page-title">Rekap Beban Mengajar</h1>
        <p class="page-subtitle">Rekapitulasi jam mengajar guru per semester</p>
    </div>
</div>

{{-- Filter & Generate Action Card --}}
<div class="surface-white-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <form method="GET" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin: 0;">
            <input type="text" name="tahun_ajaran" class="form-control" style="width: 220px;" placeholder="Tahun Ajaran (Mis: 2025/2026)" value="{{ request('tahun_ajaran') }}">
            @if(auth()->user()->isPengurusYayasan())
            <select name="jenjang" class="form-control" style="width: auto;">
                <option value="">Semua Jenjang</option>
                <option value="SD" {{ request('jenjang') === 'SD' ? 'selected' : '' }}>Unit SD</option>
                <option value="MTs" {{ request('jenjang') === 'MTs' ? 'selected' : '' }}>Unit MTs</option>
                <option value="SMK" {{ request('jenjang') === 'SMK' ? 'selected' : '' }}>Unit SMK</option>
            </select>
            @endif
            <button type="submit" class="btn-pill-action btn-pill-secondary">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                <span>Filter</span>
            </button>
            @if(request()->anyFilled(['tahun_ajaran', 'jenjang']))
            <a href="{{ route('rekap-beban.index') }}" class="btn-pill-action btn-pill-secondary" style="color: #ef4444;" title="Reset Filter">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                <span>Reset</span>
            </a>
            @endif
        </form>

        @if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
        <div>
            <button type="button" class="btn-forest-primary" onclick="openGenerateModal()" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; font-size: 13px; font-weight: 600;">
                <i data-lucide="rotate-cw" style="width: 15px; height: 15px;"></i>
                <span>Generate Rekap</span>
            </button>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Guru</th>
                    <th>Jenjang</th>
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th style="text-align:center;">Total Jam</th>
                    <th style="text-align:center;">Jumlah Kelas</th>
                    <th style="text-align:center;">Jumlah Mapel</th>
                    <th style="text-align:center;">Standar Minimal</th>
                    <th>Status Beban</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $r)
                @php
                    $statusColor = match($r->status_beban) {
                        'sesuai' => 'badge-green',
                        'kurang' => 'badge-red',
                        'lebih'  => 'badge-yellow',
                        default  => 'badge-gray',
                    };
                    $statusLabel = match($r->status_beban) {
                        'sesuai' => '✓ Sesuai',
                        'kurang' => '↓ Kurang',
                        'lebih'  => '↑ Lebih',
                        default  => ucfirst($r->status_beban),
                    };
                @endphp
                <tr>
                    <td>{{ $rekaps->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;">
                                {{ strtoupper(substr($r->guru->user->name ?? '-', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $r->guru->user->name ?? '-' }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $r->guru->spesialisasi ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="jenjang-{{ strtolower($r->guru->jenjang ?? '') }}">{{ $r->guru->jenjang ?? '-' }}</span></td>
                    <td>{{ $r->tahun_ajaran }}</td>
                    <td>{{ $r->semester }}</td>
                    <td style="text-align:center;">
                        <span style="font-size:16px;font-weight:800;color:{{ $r->status_beban === 'kurang' ? 'var(--red)' : ($r->status_beban === 'lebih' ? 'var(--yellow)' : 'var(--primary)') }};">
                            {{ $r->total_jam_mengajar }}
                        </span>
                        <span style="font-size:12px;color:var(--text-muted);"> jam</span>
                    </td>
                    <td style="text-align:center;font-weight:600;">{{ $r->jumlah_kelas }}</td>
                    <td style="text-align:center;font-weight:600;">{{ $r->jumlah_mapel }}</td>
                    <td style="text-align:center;color:var(--text-muted);">{{ $r->standar_jam_minimal }} jam</td>
                    <td><span class="badge {{ $statusColor }}">{{ $statusLabel }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <h3>Belum ada rekap beban mengajar</h3>
                            <p>Klik "Generate Rekap" untuk menghitung beban mengajar guru secara otomatis</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rekaps->hasPages())
    <div class="pagination">
        <div class="pagination-info">Menampilkan {{ $rekaps->firstItem() }}–{{ $rekaps->lastItem() }} dari {{ $rekaps->total() }} data</div>
        <div class="pagination-links">
            @if(!$rekaps->onFirstPage())<a href="{{ $rekaps->previousPageUrl() }}" class="page-link">←</a>@endif
            @foreach($rekaps->getUrlRange(1, $rekaps->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page == $rekaps->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($rekaps->hasMorePages())<a href="{{ $rekaps->nextPageUrl() }}" class="page-link">→</a>@endif
        </div>
    </div>
    @endif
</div>

{{-- Legenda --}}
<div class="card" style="margin-top:16px;">
    <div class="card-title" style="margin-bottom:12px;">Keterangan Status Beban</div>
    <div style="display:flex;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span class="badge badge-green">✓ Sesuai</span>
            Jam mengajar sesuai standar (24–28 jam/minggu)
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span class="badge badge-red">↓ Kurang</span>
            Jam mengajar kurang dari standar minimum
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span class="badge badge-yellow">↑ Lebih</span>
            Jam mengajar melebihi standar maksimum
        </div>
    </div>
</div>

{{-- Generate Modal --}}
@if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
<div class="modal-overlay" id="generateModal" onclick="if(event.target===this)closeGenerateModal()">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Generate Rekap Beban Mengajar</div>
            <button type="button" class="modal-close" onclick="closeGenerateModal()">&times;</button>
        </div>
        <p style="font-size:13.5px;color:var(--text-secondary);margin-bottom:20px;">
            Sistem akan menghitung otomatis total jam mengajar setiap guru berdasarkan jadwal yang aktif.
        </p>
        <form method="POST" action="{{ route('rekap-beban.generate') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Sekolah <span class="req">*</span></label>
                <select name="sekolah_id" class="form-control" required>
                    @if($sekolahs->count() > 1)
                    <option value="">-- Pilih Sekolah --</option>
                    @endif
                    @foreach($sekolahs as $s)
                    <option value="{{ $s->id }}" {{ (string)old('sekolah_id', $defaultSekolahId ?? '') === (string)$s->id ? 'selected' : '' }}>
                        {{ $s->nama_sekolah }} ({{ $s->jenjang }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="req">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ request('tahun_ajaran', '2025/2026') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester <span class="req">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--card-border); padding-top:16px;">
                <button type="button" class="btn-pill-action btn-pill-secondary" onclick="closeGenerateModal()">Batal</button>
                <button type="submit" class="btn-forest-primary" style="padding:8px 18px; font-size:13px;">
                    <i data-lucide="rotate-cw" style="width:14px; height:14px;"></i>
                    <span>Generate Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openGenerateModal() {
        const modal = document.getElementById('generateModal');
        if (modal) {
            modal.classList.add('active');
            if (window.lucide) lucide.createIcons();
        }
    }
    function closeGenerateModal() {
        const modal = document.getElementById('generateModal');
        if (modal) {
            modal.classList.remove('active');
        }
    }
</script>
@endif
@endsection
