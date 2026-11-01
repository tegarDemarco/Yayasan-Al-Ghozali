@extends('layouts.app')
@section('title','Rekap Beban Mengajar')
@section('page-title','Rekap Beban Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Rekap Beban Mengajar</h1>
        <p>Rekapitulasi jam mengajar guru per semester</p>
    </div>
    @if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
    <button class="btn btn-primary" onclick="document.getElementById('generateModal').classList.add('active')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
        Generate Rekap
    </button>
    @endif
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET">
        <div class="filter-bar">
            <input type="text" name="tahun_ajaran" class="form-control" style="width:180px;" placeholder="Tahun Ajaran (2025/2026)" value="{{ request('tahun_ajaran') }}">
            @if(auth()->user()->isPengurusYayasan())
            <select name="jenjang" class="form-control" style="width:auto;">
                <option value="">Semua Jenjang</option>
                <option value="SD" {{ request('jenjang')==='SD'?'selected':'' }}>SD</option>
                <option value="MTs" {{ request('jenjang')==='MTs'?'selected':'' }}>MTs</option>
                <option value="SMK" {{ request('jenjang')==='SMK'?'selected':'' }}>SMK</option>
            </select>
            @endif
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['tahun_ajaran','jenjang']))
            <a href="{{ route('rekap-beban.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>
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
<div class="modal-overlay" id="generateModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Generate Rekap Beban Mengajar</div>
            <button class="modal-close" onclick="document.getElementById('generateModal').classList.remove('active')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <p style="font-size:13.5px;color:var(--text-secondary);margin-bottom:20px;">
            Sistem akan menghitung otomatis total jam mengajar setiap guru berdasarkan jadwal yang aktif.
        </p>
        <form method="POST" action="{{ route('rekap-beban.generate') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Sekolah <span class="req">*</span></label>
                <select name="sekolah_id" class="form-control" required>
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($sekolahs as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_sekolah }} ({{ $s->jenjang }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="req">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="2025/2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester <span class="req">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('generateModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Generate Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
