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

    $pageTitle = $user->isKepalaSekolah() ? 'Validasi RPP / Modul Ajar' : 'RPP & Modul Ajar';
@endphp

@section('title', $pageTitle)
@section('page-title', $user->isKepalaSekolah() ? 'Validasi RPP / Modul' : 'RPP & Modul Ajar')

@section('content')

<!-- Page Header & Eyebrow -->
<div class="page-header-block">
    <div>
        <div class="page-eyebrow">{{ $jenjang }} · {{ $roleTitle }}</div>
        <h1 class="page-main-title">{{ $pageTitle }}</h1>
        <div class="page-sub-caption">Tinjau dokumen perencanaan sebelum digunakan dalam pembelajaran.</div>
    </div>
    @if(auth()->user()->isGuru())
    <div>
        <a href="{{ route('rpp.create') }}" class="btn-forest-primary">
            <i data-lucide="upload-cloud" style="width:16px;height:16px;"></i>
            <span>Upload Dokumen Ajar</span>
        </a>
    </div>
    @endif
</div>

<!-- Info Callout Box (as in wireframe) -->
<div class="info-callout-box">
    <i data-lucide="info" style="width:18px;height:18px;color:var(--forest-600);flex-shrink:0;"></i>
    <span>Alur validasi: guru mengunggah dokumen &rarr; kepala sekolah memeriksa &rarr; status valid atau revisi dikirim kembali ke guru. Dokumen valid dapat diarsipkan admin.</span>
</div>

<!-- Main Table Surface Card -->
<div class="surface-white-card">
    <!-- Filter Bar -->
    <form method="GET" action="{{ route('rpp.index') }}" autocomplete="off">
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
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="revisi" {{ in_array(request('status'), ['revisi', 'ditolak']) ? 'selected' : '' }}>Revisi</option>
                    <option value="valid" {{ in_array(request('status'), ['valid', 'disetujui']) ? 'selected' : '' }}>Valid</option>
                </select>

                @if(request()->anyFilled(['search', 'status', 'tipe', 'guru_id']))
                <a href="{{ route('rpp.index') }}" style="font-size:12.5px;color:var(--forest-700);text-decoration:none;font-weight:600;white-space:nowrap;">
                    Reset Filter
                </a>
                @endif
            </div>

            <div class="filter-data-count">
                {{ $rpps->total() }} data dokumen
            </div>
        </div>
    </form>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="wireframe-table">
            <thead>
                <tr>
                    <th style="width: 32%;">Dokumen</th>
                    <th style="width: 22%;">Pengunggah</th>
                    <th style="width: 20%;">Mata Pelajaran</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 14%; text-align: right;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rpps as $rpp)
                @php
                    $isDisetujui = ($rpp->status_validasi === 'disetujui');
                    $isDitolak = ($rpp->status_validasi === 'ditolak');
                    $isMenunggu = ($rpp->status_validasi === 'menunggu');
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;color:var(--text-heading);">
                            @if($rpp->file_path)
                                <a href="{{ asset('storage/'.$rpp->file_path) }}" target="_blank" style="color:var(--text-heading);text-decoration:none;">
                                    {{ $rpp->judul }}
                                </a>
                            @else
                                {{ $rpp->judul }}
                            @endif
                        </div>
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                            {{ $rpp->tipe }} · Kelas {{ $rpp->kelas }} ({{ $rpp->semester }})
                        </div>
                    </td>

                    <td>
                        <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->guru?->user?->name ?? 'Guru' }}</div>
                        <div style="font-size:11px;color:var(--text-subtle);">{{ $rpp->guru?->nip ?? 'NIP -' }}</div>
                    </td>

                    <td>
                        <span style="font-weight:500;">{{ $rpp->mata_pelajaran }}</span>
                    </td>

                    <td>
                        @if($isMenunggu)
                            <span class="pill-status pill-status-menunggu">Menunggu</span>
                        @elseif($isDitolak)
                            <span class="pill-status pill-status-revisi">Revisi</span>
                        @elseif($isDisetujui)
                            <span class="pill-status pill-status-valid">Valid</span>
                        @endif
                    </td>

                    <td style="text-align: right;">
                        <div style="display:inline-flex;gap:6px;align-items:center;flex-wrap:wrap;justify-content:flex-end;">
                            {{-- Detail button for all roles --}}
                            <a href="{{ route('rpp.show', $rpp) }}" class="btn-pill-action btn-pill-secondary" title="Lihat Detail">
                                <i data-lucide="eye" style="width:13px;height:13px;"></i>
                                <span>Detail</span>
                            </a>

                            @if(auth()->user()->isKepalaSekolah())
                                {{-- Kepala Sekolah Actions --}}
                                @if($isDisetujui)
                                    <span class="btn-pill-action btn-pill-done">
                                        • Valid
                                    </span>
                                @else
                                    {{-- Validasi form --}}
                                    <form method="POST" action="{{ route('rpp.validasi', $rpp) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="status_validasi" value="disetujui">
                                        <button type="submit" class="btn-pill-action btn-pill-primary" title="Setujui Dokumen">
                                            Validasi
                                        </button>
                                    </form>

                                    {{-- Revisi button triggers modal --}}
                                    <button type="button" class="btn-pill-action btn-pill-danger"
                                            onclick="openRevisiModal({{ $rpp->id }}, '{{ addslashes($rpp->judul) }}')">
                                        Revisi
                                    </button>
                                @endif
                            @else
                                {{-- Guru / Admin actions --}}
                                @if($rpp->file_path)
                                    <a href="{{ asset('storage/'.$rpp->file_path) }}" target="_blank" class="btn-pill-action btn-pill-secondary">
                                        <i data-lucide="download" style="width:13px;height:13px;"></i>
                                        <span>Unduh</span>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 36px; color: var(--text-muted);">
                        Belum ada dokumen yang sesuai kriteria pencarian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rpps->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 12px; color: var(--text-muted);">
            Menampilkan {{ $rpps->firstItem() }}–{{ $rpps->lastItem() }} dari {{ $rpps->total() }} dokumen
        </span>
        <div>
            {{ $rpps->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Prototype Footnote -->
<div class="footnote-prototype">
    Data pada prototipe ini bersifat ilustratif, bukan data operasional.
</div>

<!-- Modal Dialog Revisi (Kepala Sekolah) -->
<div class="modal-overlay" id="revisiModal" onclick="if(event.target===this)closeRevisiModal()">
    <div class="modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div class="modal-title">Kirim Catatan Revisi</div>
            <button type="button" onclick="closeRevisiModal()" style="background:none;border:none;cursor:pointer;color:var(--text-subtle);font-size:18px;">&times;</button>
        </div>

        <form id="revisiForm" method="POST" action="">
            @csrf
            <input type="hidden" name="status_validasi" value="ditolak">
            <div style="font-size:12.5px;color:var(--text-muted);margin-bottom:12px;" id="modalDokumenName"></div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;color:var(--text-heading);">
                    Catatan Perbaikan / Revisi
                </label>
                <textarea name="catatan_validasi" class="form-control-input" rows="4" style="height:auto;padding:10px;"
                          placeholder="Tuliskan bagian yang perlu diperbaiki oleh guru..." required></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" class="btn-pill-action btn-pill-secondary" onclick="closeRevisiModal()">Batal</button>
                <button type="submit" class="btn-forest-primary" style="background:#dc2626;">Kirim Revisi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRevisiModal(id, title) {
        document.getElementById('modalDokumenName').textContent = 'Dokumen: ' + title;
        document.getElementById('revisiForm').action = '/rpp/' + id + '/validasi';
        document.getElementById('revisiModal').style.display = 'flex';
    }
    function closeRevisiModal() {
        document.getElementById('revisiModal').style.display = 'none';
    }
</script>
@endpush

@endsection
