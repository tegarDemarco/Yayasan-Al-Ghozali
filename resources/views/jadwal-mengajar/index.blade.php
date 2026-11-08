@extends('layouts.app')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')

@section('content')
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

<!-- Page Header & Eyebrow -->
<div class="page-header-block">
    <div>
        <div class="page-eyebrow">{{ $jenjang }} · {{ $roleTitle }}</div>
        <h1 class="page-main-title">Jadwal Mengajar</h1>
        <div class="page-sub-caption">Pengaturan distribusi jam pelajaran per guru dan kelas.</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        @if(request('guru_id'))
            <a href="{{ route('guru.show', request('guru_id')) }}" class="btn-pill-action btn-pill-secondary" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;text-decoration:none;font-weight:600;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                <span>Kembali ke Detail Guru</span>
            </a>
        @elseif(url()->previous() && str_contains(url()->previous(), '/guru/') && url()->previous() !== url()->current())
            <a href="{{ url()->previous() }}" class="btn-pill-action btn-pill-secondary" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;text-decoration:none;font-weight:600;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                <span>Kembali ke Detail Guru</span>
            </a>
        @elseif(!auth()->user()->isGuru())
            <a href="{{ route('guru.index') }}" class="btn-pill-action btn-pill-secondary" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;text-decoration:none;font-weight:600;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                <span>Kembali ke Data Guru</span>
            </a>
        @endif

        @if(!auth()->user()->isGuru())
        <a href="{{ route('jadwal-mengajar.create') }}" class="btn-forest-primary">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            <span>Tambah Jadwal</span>
        </a>
        @endif
    </div>
</div>

<!-- Main Table Surface Card -->
<div class="surface-white-card">
    @if(request('guru_id'))
    @php
        $selectedGuru = $gurus->firstWhere('id', request('guru_id')) ?? \App\Models\Guru::with('user')->find(request('guru_id'));
    @endphp
    @if($selectedGuru)
    <div style="margin: 0 0 16px 0; padding: 10px 16px; background: var(--forest-50, #f0fdf4); border: 1px solid var(--forest-200, #bbf7d0); border-radius: 8px; display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
        <div style="display: flex; align-items: center; gap: 8px; color: var(--forest-800, #166534);">
            <i data-lucide="user-check" style="width: 16px; height: 16px;"></i>
            <span>Menampilkan jadwal mengajar untuk: <strong>{{ $selectedGuru->user?->name ?? 'Guru' }}</strong> ({{ $selectedGuru->jenjang }})</span>
        </div>
        <a href="{{ route('jadwal-mengajar.index') }}" style="font-size: 12px; color: #dc2626; text-decoration: none; font-weight: 600;">
            Lihat Semua Guru &times;
        </a>
    </div>
    @endif
    @endif

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('jadwal-mengajar.index') }}" autocomplete="off">
        @if(request('guru_id'))
            <input type="hidden" name="guru_id" value="{{ request('guru_id') }}">
        @endif
        <div class="table-filter-bar">
            <div class="filter-controls-left">
                <div class="search-input-wrap">
                    <i data-lucide="search"></i>
                    <input type="text" name="search" class="form-control-input"
                           placeholder="Cari nama atau keterangan..."
                           value="{{ request('search') }}">
                </div>

                <select name="status" class="form-control-select" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="terpublikasi" {{ request('status') === 'terpublikasi' ? 'selected' : '' }}>Terpublikasi</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                @if(request()->anyFilled(['search', 'status', 'hari']))
                <a href="{{ route('jadwal-mengajar.index', request('guru_id') ? ['guru_id' => request('guru_id')] : []) }}" style="font-size:12.5px;color:var(--forest-700);text-decoration:none;font-weight:600;white-space:nowrap;">
                    Reset Filter
                </a>
                @elseif(request()->filled('guru_id'))
                <a href="{{ route('jadwal-mengajar.index') }}" style="font-size:12.5px;color:var(--forest-700);text-decoration:none;font-weight:600;white-space:nowrap;">
                    Tampilkan Semua Jadwal
                </a>
                @endif
            </div>

            <div class="filter-data-count">
                {{ $jadwals->total() }} data jadwal
            </div>
        </div>
    </form>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="wireframe-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Hari</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 25%;">Mata Pelajaran</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 18%;">Guru</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 10%; text-align: right;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                <tr>
                    <td><strong>{{ $jadwal->hari }}</strong></td>
                    <td style="color:var(--text-muted);font-size:12.5px;white-space:nowrap;">
                        {{ substr($jadwal->jam_mulai, 0, 5) }}–{{ substr($jadwal->jam_selesai, 0, 5) }}
                    </td>
                    <td>
                        <strong style="color:var(--text-heading);">{{ $jadwal->mata_pelajaran }}</strong>
                        @if($jadwal->ruangan)
                            <span style="font-size:11.5px;color:var(--text-subtle);"> · {{ $jadwal->ruangan }}</span>
                        @endif
                    </td>
                    <td>{{ $jadwal->kelas }}</td>
                    <td>{{ $jadwal->guru?->user?->name ?? 'Belum ditentukan' }}</td>
                    <td>
                        @if($jadwal->is_aktif)
                            <span class="pill-status pill-status-terpublikasi">Terpublikasi</span>
                        @else
                            <span class="pill-status pill-status-draft">Draft</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!auth()->user()->isGuru())
                            {{-- Admin actions --}}
                            @if(!$jadwal->is_aktif)
                                <form method="POST" action="{{ route('jadwal-mengajar.publish', $jadwal) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-pill-action btn-pill-primary" title="Publikasikan Jadwal">
                                        Publikasikan
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('jadwal-mengajar.edit', $jadwal) }}" class="btn-pill-action btn-pill-secondary">
                                    Ubah
                                </a>
                            @endif
                        @else
                            {{-- Guru actions --}}
                            <button type="button" class="btn-pill-action btn-pill-secondary" onclick="showJadwalDetail({{ json_encode($jadwal) }})">
                                Lihat
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-muted);">
                        Belum ada jadwal mengajar yang sesuai filter
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($jadwals->hasPages())
    <div class="pagination" style="padding-top: 16px; border-top: 1px solid var(--card-border, #e2e8f0); margin-top: 16px;">
        <div class="pagination-info">
            Menampilkan {{ $jadwals->firstItem() }}–{{ $jadwals->lastItem() }} dari {{ $jadwals->total() }} data jadwal
        </div>
        <div>
            {{ $jadwals->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Prototype Footnote -->
<div class="footnote-prototype">
    Data pada prototipe ini bersifat ilustratif, bukan data operasional.
</div>

<!-- Detail Modal for Guru -->
<div class="modal-overlay" id="jadwalModal" onclick="if(event.target===this)closeJadwalModal()">
    <div class="modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div class="modal-title" id="modalMapelTitle">Detail Jadwal Mengajar</div>
            <button type="button" onclick="closeJadwalModal()" style="background:none;border:none;cursor:pointer;color:var(--text-subtle);font-size:18px;">&times;</button>
        </div>
        <div id="modalDetailBody" style="font-size:13px;color:var(--text-body);line-height:1.7;">
            <!-- Injected by JS -->
        </div>
        <div style="text-align:right;margin-top:18px;">
            <button type="button" class="btn-forest-primary" onclick="closeJadwalModal()">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showJadwalDetail(item) {
        document.getElementById('modalMapelTitle').textContent = item.mata_pelajaran + ' - ' + item.kelas;
        document.getElementById('modalDetailBody').innerHTML = `
            <p><strong>Hari:</strong> ${item.hari}</p>
            <p><strong>Waktu:</strong> ${item.jam_mulai.substring(0,5)} – ${item.jam_selesai.substring(0,5)}</p>
            <p><strong>Ruangan:</strong> ${item.ruangan || 'Ruang Standar'}</p>
            <p><strong>Tahun Ajaran:</strong> ${item.tahun_ajaran} (${item.semester})</p>
            <p><strong>Status:</strong> ${item.is_aktif ? '<span class="pill-status pill-status-terpublikasi">Terpublikasi</span>' : '<span class="pill-status pill-status-draft">Draft</span>'}</p>
        `;
        document.getElementById('jadwalModal').style.display = 'flex';
    }
    function closeJadwalModal() {
        document.getElementById('jadwalModal').style.display = 'none';
    }
</script>
@endpush

@endsection
