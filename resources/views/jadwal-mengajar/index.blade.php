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
                    <th style="width: 22%;">Mata Pelajaran</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 16%;">Guru</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 15%; text-align: right;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                <tr>
                    <td><strong>{{ $jadwal->hari }}</strong></td>
                    <td style="color:var(--text-muted);font-size:12.5px;white-space:nowrap;">
                        {{ $jadwal->formatted_waktu }}
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
                    <td style="text-align: right; white-space: nowrap;">
                        @if(!auth()->user()->isGuru())
                            <div style="display: inline-flex; align-items: center; gap: 6px;">
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

                                {{-- Tombol Hapus --}}
                                <form method="POST" action="{{ route('jadwal-mengajar.destroy', $jadwal) }}" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal {{ $jadwal->mata_pelajaran }} ({{ $jadwal->kelas }}) ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-pill-action" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; cursor: pointer;" title="Hapus Jadwal">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Guru actions --}}
                            <button type="button"
                                    class="btn-pill-action btn-pill-secondary btn-detail-jadwal"
                                    data-mapel="{{ $jadwal->mata_pelajaran }}"
                                    data-kelas="{{ $jadwal->kelas }}"
                                    data-hari="{{ $jadwal->hari }}"
                                    data-waktu="{{ $jadwal->formatted_waktu }} WIB"
                                    data-ruangan="{{ $jadwal->ruangan ?? 'Ruang Standar' }}"
                                    data-guru="{{ $jadwal->guru?->user?->name ?? 'Belum ditentukan' }}"
                                    data-sekolah="{{ $jadwal->sekolah?->nama ?? ($jadwal->guru?->jenjang ? 'Unit ' . $jadwal->guru->jenjang : 'Yayasan Al Ghozaly') }}"
                                    data-tahun="{{ $jadwal->tahun_ajaran }}"
                                    data-semester="{{ $jadwal->semester }}"
                                    data-status="{{ $jadwal->is_aktif ? 'terpublikasi' : 'draft' }}"
                                    onclick="openJadwalDetailFromBtn(this)">
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
    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--card-border, #e2e8f0); padding-top: 16px;">
        <span style="font-size: 12.5px; color: var(--text-muted);">
            Menampilkan {{ $jadwals->firstItem() }}–{{ $jadwals->lastItem() }} dari {{ $jadwals->total() }} data jadwal
        </span>
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

<!-- Modal Detail Jadwal Mengajar (Guru) -->
<div class="modal-overlay" id="jadwalModal" onclick="if(event.target===this)closeJadwalModal()">
    <div class="modal-box modal-box-lg" style="max-width: 520px;">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalMapelTitle" style="margin-bottom:2px;">Detail Jadwal Mengajar</div>
                <div style="font-size:12px;color:var(--text-subtle);" id="modalMapelSubtitle">Informasi lengkap jadwal pembelajaran</div>
            </div>
            <button type="button" class="modal-close" onclick="closeJadwalModal()">&times;</button>
        </div>

        <div id="modalDetailBody" style="font-size:13px;line-height:1.7;">
            <!-- Konten diinjeksi via JavaScript -->
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:20px;">
            <button type="button" class="btn-forest-primary" onclick="closeJadwalModal()">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function openJadwalDetailFromBtn(btn) {
    const item = {
        mata_pelajaran: btn.getAttribute('data-mapel'),
        kelas: btn.getAttribute('data-kelas'),
        hari: btn.getAttribute('data-hari'),
        waktu: btn.getAttribute('data-waktu'),
        ruangan: btn.getAttribute('data-ruangan'),
        guru: btn.getAttribute('data-guru'),
        sekolah: btn.getAttribute('data-sekolah'),
        tahun_ajaran: btn.getAttribute('data-tahun'),
        semester: btn.getAttribute('data-semester'),
        status: btn.getAttribute('data-status'),
        is_aktif: btn.getAttribute('data-status') === 'terpublikasi'
    };
    showJadwalDetail(item);
}

function showJadwalDetail(item) {
    if (!item) return;

    const mapel = item.mata_pelajaran || '-';
    const kelas = item.kelas || '-';
    const hari = item.hari || '-';
    
    let waktu = item.waktu;
    if (!waktu) {
        if (item.formatted_waktu) {
            waktu = item.formatted_waktu + ' WIB';
        } else if (item.jam_mulai && item.jam_selesai) {
            const m = String(item.jam_mulai).substring(0, 5);
            const s = String(item.jam_selesai).substring(0, 5);
            waktu = m + ' – ' + s + ' WIB';
        } else {
            waktu = '-';
        }
    }

    const ruangan = item.ruangan || 'Ruang Standar';
    const guru = item.guru || (item.guru && item.guru.user ? item.guru.user.name : null) || '-';
    const sekolah = item.sekolah || (item.sekolah ? item.sekolah.nama : null) || 'Yayasan Al Ghozaly';
    const tahun = item.tahun_ajaran ? (item.tahun_ajaran + ' (' + (item.semester || '-') + ')') : (item.semester || '-');
    const isAktif = (item.is_aktif === true || item.is_aktif === 1 || item.status === 'terpublikasi');

    const titleEl = document.getElementById('modalMapelTitle');
    const subTitleEl = document.getElementById('modalMapelSubtitle');
    const bodyEl = document.getElementById('modalDetailBody');
    const modalEl = document.getElementById('jadwalModal');

    if (titleEl) titleEl.textContent = mapel;
    if (subTitleEl) subTitleEl.textContent = 'Kelas ' + kelas + ' · ' + sekolah;

    if (bodyEl) {
        bodyEl.innerHTML = `
            <div style="display:grid;grid-template-columns:130px 1fr;gap:10px 14px;background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
                <div style="color:var(--text-subtle);">Mata Pelajaran:</div>
                <div style="font-weight:600;color:var(--text-heading);">${escapeHtml(mapel)}</div>

                <div style="color:var(--text-subtle);">Kelas:</div>
                <div style="font-weight:600;color:var(--text-heading);">${escapeHtml(kelas)}</div>

                <div style="color:var(--text-subtle);">Hari:</div>
                <div style="font-weight:600;color:var(--forest-800);">${escapeHtml(hari)}</div>

                <div style="color:var(--text-subtle);">Waktu Belajar:</div>
                <div style="font-weight:600;color:var(--text-heading);">${escapeHtml(waktu)}</div>

                <div style="color:var(--text-subtle);">Ruangan:</div>
                <div>${escapeHtml(ruangan)}</div>

                <div style="color:var(--text-subtle);">Guru Pengampu:</div>
                <div style="font-weight:600;color:var(--text-heading);">${escapeHtml(guru)}</div>

                <div style="color:var(--text-subtle);">Tahun Ajaran:</div>
                <div>${escapeHtml(tahun)}</div>

                <div style="color:var(--text-subtle);">Status:</div>
                <div>${isAktif ? '<span class="pill-status pill-status-terpublikasi">Terpublikasi</span>' : '<span class="pill-status pill-status-draft">Draft</span>'}</div>
            </div>
        `;
    }

    if (modalEl) {
        modalEl.classList.add('active');
        modalEl.style.display = 'flex';
    }
}

function closeJadwalModal() {
    const modalEl = document.getElementById('jadwalModal');
    if (modalEl) {
        modalEl.classList.remove('active');
        modalEl.style.display = 'none';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeJadwalModal();
    }
});
</script>
@endpush
@endsection