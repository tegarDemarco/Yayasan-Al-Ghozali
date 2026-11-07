@extends('layouts.app')

@php
    $user = auth()->user();
    $role = $user->role;
    $jenjang = $user->jenjang ?? 'Yayasan';

    $roleTitles = [
        'pengurus_yayasan' => 'Pengurus Yayasan',
        'kepala_sekolah' => 'Kepala Sekolah',
        'admin_sekolah' => 'Admin Sekolah',
        'guru' => 'Guru',
    ];
    $roleTitle = $roleTitles[$role] ?? 'Pengguna';
@endphp

@section('title', 'Pengajuan Izin & Cuti')
@section('page-title', 'Pengajuan Izin & Cuti')

@section('content')

<!-- Page Header & Action -->
<div class="page-header-block">
    <div>
        <div class="page-eyebrow">{{ $jenjang }} · {{ $roleTitle }}</div>
        <h1 class="page-main-title">Pengajuan Izin & Cuti</h1>
        <div class="page-sub-caption">
            {{ $user->isGuru() ? 'Kelola dan pantau permohonan izin atau cuti kerja Anda.' : 'Pemeriksaan dan persetujuan permohonan izin atau cuti guru.' }}
        </div>
    </div>
    @if($user->isGuru())
    <div>
        <a href="{{ route('pengajuan-izin.create') }}" class="btn-forest-primary">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            <span>Buat Pengajuan</span>
        </a>
    </div>
    @endif
</div>

<!-- Main Surface Card -->
<div class="surface-white-card">
    <!-- Filter Bar -->
    <form method="GET" action="{{ route('pengajuan-izin.index') }}" autocomplete="off" style="margin-bottom:16px;">
        <div class="filter-bar" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <div style="flex:1;min-width:200px;">
                <input type="text" name="search" class="form-control-input" placeholder="Cari nama guru atau alasan..." value="{{ request('search') }}" style="width:100%;height:38px;">
            </div>

            <div style="min-width:140px;">
                <select name="status" class="form-control-input" style="width:100%;height:38px;">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>✓ Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>✗ Ditolak</option>
                </select>
            </div>

            <div style="min-width:140px;">
                <select name="jenis" class="form-control-input" style="width:100%;height:38px;">
                    <option value="">Semua Jenis</option>
                    @foreach(['Izin', 'Cuti', 'Sakit', 'Tugas Luar', 'Dinas'] as $j)
                    <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-forest-primary" style="height:38px;padding:0 16px;">
                <i data-lucide="filter" style="width:14px;height:14px;"></i>
                <span>Filter</span>
            </button>

            @if(request()->anyFilled(['search', 'status', 'jenis']))
            <a href="{{ route('pengajuan-izin.index') }}" class="btn-pill-action btn-pill-secondary" style="height:38px;display:inline-flex;align-items:center;padding:0 14px;">
                Reset
            </a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="wireframe-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    @if(!$user->isGuru())
                    <th style="width: 180px;">Guru</th>
                    @endif
                    <th style="width: 110px;">Jenis</th>
                    <th style="width: 170px;">Periode Tanggal</th>
                    <th style="width: 80px; text-align: center;">Durasi</th>
                    <th>Alasan</th>
                    <th style="width: 90px; text-align: center;">Lampiran</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 220px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jenisColors = [
                        'Izin' => 'badge-blue',
                        'Cuti' => 'badge-purple',
                        'Sakit' => 'badge-yellow',
                        'Tugas Luar' => 'badge-orange',
                        'Dinas' => 'badge-green',
                    ];
                @endphp
                @forelse($pengajuans as $p)
                @php
                    $isMenunggu = $p->status_approval === 'menunggu';
                    $isDisetujui = $p->status_approval === 'disetujui';
                    $isDitolak = $p->status_approval === 'ditolak';

                    $itemData = [
                        'id' => $p->id,
                        'guru' => $p->guru?->user?->name ?? 'Guru',
                        'jenjang' => $p->guru?->jenjang ?? $p->sekolah?->jenjang ?? '-',
                        'nip' => $p->guru?->nip ?? '-',
                        'jenis' => $p->jenis_pengajuan,
                        'periode' => $p->tanggal_mulai->format('d/m/Y') . ' – ' . $p->tanggal_selesai->format('d/m/Y'),
                        'hari' => $p->jumlah_hari,
                        'alasan' => $p->alasan,
                        'lampiran_url' => $p->lampiran ? asset('storage/' . $p->lampiran) : null,
                        'status' => $p->status_approval,
                        'approver' => $p->approver?->name,
                        'disetujui_at' => $p->disetujui_at ? $p->disetujui_at->format('d/m/Y H:i') : null,
                        'catatan' => $p->catatan_approval,
                        'diajukan' => $p->created_at->format('d/m/Y H:i'),
                    ];
                @endphp
                <tr>
                    <td>{{ $pengajuans->firstItem() + $loop->index }}</td>

                    @if(!$user->isGuru())
                    <td>
                        <div style="font-weight:600;color:var(--text-heading);">{{ $p->guru?->user?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--text-subtle);">
                            {{ $p->guru?->nip ?? 'NIP -' }} · <span class="jenjang-{{ strtolower($p->guru?->jenjang ?? '') }}">{{ $p->guru?->jenjang ?? '' }}</span>
                        </div>
                    </td>
                    @endif

                    <td>
                        <span class="badge {{ $jenisColors[$p->jenis_pengajuan] ?? 'badge-gray' }}">
                            {{ $p->jenis_pengajuan }}
                        </span>
                    </td>

                    <td>
                        <div style="font-weight:500;">{{ $p->tanggal_mulai->format('d/m/Y') }} – {{ $p->tanggal_selesai->format('d/m/Y') }}</div>
                        <div style="font-size:11px;color:var(--text-subtle);">Diajukan {{ $p->created_at->format('d/m/Y') }}</div>
                    </td>

                    <td style="text-align: center; font-weight: 700; color: var(--forest-800);">
                        {{ $p->jumlah_hari }} hari
                    </td>

                    <td>
                        <span title="{{ $p->alasan }}">{{ \Str::limit($p->alasan, 40) }}</span>
                    </td>

                    <td style="text-align: center;">
                        @if($p->lampiran)
                        <a href="{{ asset('storage/' . $p->lampiran) }}" target="_blank" class="btn-pill-action btn-pill-secondary" title="Lihat Lampiran" style="padding:3px 8px;font-size:11.5px;">
                            <i data-lucide="paperclip" style="width:13px;height:13px;"></i>
                            <span>File</span>
                        </a>
                        @else
                        <span style="color:var(--text-subtle);font-size:12px;">-</span>
                        @endif
                    </td>

                    <td>
                        @if($isMenunggu)
                            <span class="pill-status pill-status-menunggu">⏳ Menunggu</span>
                        @elseif($isDisetujui)
                            <span class="pill-status pill-status-valid">✓ Disetujui</span>
                        @elseif($isDitolak)
                            <span class="pill-status pill-status-revisi">✗ Ditolak</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($p->status_approval) }}</span>
                        @endif
                    </td>

                    <td style="text-align: right;">
                        <div style="display:inline-flex;gap:6px;align-items:center;justify-content:flex-end;flex-wrap:wrap;">
                            {{-- Kepala Sekolah / Admin Sekolah Actions --}}
                            @if($user->isKepalaSekolah() || $user->isAdminSekolah())
                                @if($isMenunggu)
                                    {{-- Quick Setujui Action --}}
                                    <form method="POST" action="{{ route('pengajuan-izin.approve', $p) }}" style="display:inline;" onsubmit="return confirm('Setujui pengajuan izin ini?')">
                                        @csrf
                                        <input type="hidden" name="status_approval" value="disetujui">
                                        <input type="hidden" name="catatan_approval" value="Disetujui">
                                        <button type="submit" class="btn-pill-action btn-pill-primary" title="Setujui Langsung">
                                            ✓ Setujui
                                        </button>
                                    </form>

                                    {{-- Review Modal Button --}}
                                    <button type="button" class="btn-pill-action btn-pill-danger" onclick='openApprovalModal(@json($itemData))' title="Review & Beri Keputusan">
                                        Review
                                    </button>
                                @endif
                            @endif

                            {{-- Guru Cancel Action --}}
                            @if($user->isGuru() && $isMenunggu)
                            <form method="POST" action="{{ route('pengajuan-izin.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan izin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-pill-action btn-pill-danger" title="Batalkan Pengajuan">
                                    Batalkan
                                </button>
                            </form>
                            @endif

                            {{-- Detail Button (Available for all) --}}
                            <button type="button" class="btn-pill-action btn-pill-secondary" onclick='openDetailModal(@json($itemData))' title="Lihat Rincian Pengajuan">
                                Detail
                            </button>
                        </div>

                        @if(!$isMenunggu && $p->catatan_approval)
                        <div style="font-size:11px;color:var(--text-subtle);margin-top:3px;text-align:right;">
                            💬 {{ \Str::limit($p->catatan_approval, 25) }}
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $user->isGuru() ? 8 : 9 }}">
                        <div class="empty-state-box" style="padding: 40px 20px; text-align: center;">
                            <div style="width:48px;height:48px;border-radius:12px;background:var(--forest-100);color:var(--forest-800);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <i data-lucide="calendar" style="width:24px;height:24px;"></i>
                            </div>
                            <h3 style="font-size:15px;font-weight:700;color:var(--text-heading);margin-bottom:4px;">Belum Ada Pengajuan Izin</h3>
                            <p style="font-size:12.5px;color:var(--text-subtle);max-width:360px;margin:0 auto 16px;">
                                {{ $user->isGuru() ? 'Klik tombol "Buat Pengajuan" untuk mengajukan izin atau cuti kerja baru.' : 'Belum ada pengajuan izin atau cuti dari guru pada unit ini.' }}
                            </p>
                            @if($user->isGuru())
                            <a href="{{ route('pengajuan-izin.create') }}" class="btn-forest-primary" style="display:inline-flex;">
                                <i data-lucide="plus" style="width:14px;height:14px;"></i>
                                <span>Buat Pengajuan</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pengajuans->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid #f1f5f9;margin-top:14px;">
        <div style="font-size:12.5px;color:var(--text-subtle);">
            Menampilkan {{ $pengajuans->firstItem() }}–{{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }} data
        </div>
        <div>
            {{ $pengajuans->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Review & Approval Modal (Kepala Sekolah & Admin Sekolah) --}}
@if($user->isKepalaSekolah() || $user->isAdminSekolah())
<div class="modal-overlay" id="approvalModal" onclick="if(event.target===this)closeApprovalModal()">
    <div class="modal-box modal-box-lg" style="max-width: 540px;">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="margin-bottom:2px;">Review Pengajuan Izin / Cuti</div>
                <div style="font-size:12px;color:var(--text-subtle);">Tinjau rincian permohonan dan tentukan keputusan persetujuan.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeApprovalModal()">&times;</button>
        </div>

        <!-- Ringkasan Info Pengajuan -->
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;">
            <div style="display:grid;grid-template-columns:100px 1fr;gap:6px 10px;">
                <div style="color:var(--text-subtle);">Guru:</div>
                <div id="reviewModalGuru" style="font-weight:600;color:var(--text-heading);">-</div>

                <div style="color:var(--text-subtle);">Jenis:</div>
                <div id="reviewModalJenis" style="font-weight:600;">-</div>

                <div style="color:var(--text-subtle);">Periode:</div>
                <div id="reviewModalPeriode">-</div>

                <div style="color:var(--text-subtle);">Alasan:</div>
                <div id="reviewModalAlasan" style="color:var(--text-body);font-style:italic;">-</div>

                <div style="color:var(--text-subtle);">Lampiran:</div>
                <div id="reviewModalLampiran">-</div>
            </div>
        </div>

        <form id="approvalForm" method="POST" action="">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px;color:var(--text-heading);">
                    Keputusan Persetujuan <span style="color:#ef4444;">*</span>
                </label>
                <div style="display:flex;gap:12px;">
                    <label style="flex:1;display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid #bbf7d0;background:#f0fdf4;border-radius:8px;cursor:pointer;">
                        <input type="radio" name="status_approval" value="disetujui" id="radioSetujui" required style="accent-color:#16a34a;width:16px;height:16px;">
                        <span style="font-weight:700;color:#15803d;font-size:13px;">✓ Setujui Pengajuan</span>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid #fecaca;background:#fef2f2;border-radius:8px;cursor:pointer;">
                        <input type="radio" name="status_approval" value="ditolak" id="radioTolak" style="accent-color:#dc2626;width:16px;height:16px;">
                        <span style="font-weight:700;color:#b91c1c;font-size:13px;">✗ Tolak Pengajuan</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text-heading);">
                    Catatan Keputusan <span style="font-weight:400;color:var(--text-subtle);">(opsional)</span>
                </label>
                <textarea name="catatan_approval" id="reviewCatatan" class="form-control-input" rows="3" style="width:100%;height:auto;padding:10px;font-size:13px;" placeholder="Tuliskan catatan atau instruksi keputusan untuk guru..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-pill-action btn-pill-secondary" onclick="closeApprovalModal()">Batal</button>
                <button type="submit" class="btn-forest-primary">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Detail Modal (Semua Pengguna) --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)closeDetailModal()">
    <div class="modal-box modal-box-lg" style="max-width: 560px;">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="margin-bottom:2px;">Rincian Pengajuan Izin / Cuti</div>
                <div style="font-size:12px;color:var(--text-subtle);">Informasi lengkap status dan riwayat permohonan.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>

        <div style="font-size:13px;line-height:1.7;">
            <div style="display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin-bottom:16px;background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;">
                <div style="color:var(--text-subtle);">Nama Guru:</div>
                <div id="detailGuru" style="font-weight:600;color:var(--text-heading);">-</div>

                <div style="color:var(--text-subtle);">Unit Sekolah:</div>
                <div id="detailJenjang">-</div>

                <div style="color:var(--text-subtle);">Jenis Permohonan:</div>
                <div id="detailJenis">-</div>

                <div style="color:var(--text-subtle);">Periode Tanggal:</div>
                <div id="detailPeriode">-</div>

                <div style="color:var(--text-subtle);">Total Durasi:</div>
                <div id="detailDurasi" style="font-weight:700;color:var(--forest-800);">-</div>

                <div style="color:var(--text-subtle);">Diajukan Pada:</div>
                <div id="detailDiajukan">-</div>

                <div style="color:var(--text-subtle);">Status Saat Ini:</div>
                <div id="detailStatus">-</div>

                <div style="color:var(--text-subtle);">Diproses Oleh:</div>
                <div id="detailApprover">-</div>

                <div style="color:var(--text-subtle);">Waktu Keputusan:</div>
                <div id="detailWaktuApproval">-</div>
            </div>

            <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-bottom:12px;">
                <div style="font-size:11.5px;font-weight:700;color:var(--text-subtle);text-transform:uppercase;margin-bottom:4px;">Alasan Permohonan:</div>
                <div id="detailAlasan" style="color:var(--text-heading);">-</div>
            </div>

            <div id="detailCatatanBox" style="background:#fffbeb;border:1px solid #fef3c7;border-radius:8px;padding:12px 14px;margin-bottom:12px;display:none;">
                <div style="font-size:11.5px;font-weight:700;color:#b45309;text-transform:uppercase;margin-bottom:4px;">Catatan Keputusan:</div>
                <div id="detailCatatan" style="color:#78350f;">-</div>
            </div>

            <div id="detailLampiranBox" style="margin-bottom:12px;">
                <div style="font-size:11.5px;font-weight:700;color:var(--text-subtle);text-transform:uppercase;margin-bottom:6px;">Berkas Lampiran:</div>
                <div id="detailLampiranContent">-</div>
            </div>
        </div>

        <div class="modal-footer" style="margin-top:14px;border-top:1px solid #f1f5f9;padding-top:12px;">
            <button type="button" class="btn-forest-primary" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const approveBaseUrl = "{{ url('pengajuan-izin') }}";

    function openApprovalModal(item, defaultDecision = 'disetujui') {
        const form = document.getElementById('approvalForm');
        if (form) {
            form.action = approveBaseUrl + '/' + item.id + '/approve';
        }

        const guruEl = document.getElementById('reviewModalGuru');
        if (guruEl) guruEl.textContent = item.guru + ' (' + item.jenjang + ')';

        const jenisEl = document.getElementById('reviewModalJenis');
        if (jenisEl) jenisEl.textContent = item.jenis;

        const periodeEl = document.getElementById('reviewModalPeriode');
        if (periodeEl) periodeEl.textContent = item.periode + ' (' + item.hari + ' hari)';

        const alasanEl = document.getElementById('reviewModalAlasan');
        if (alasanEl) alasanEl.textContent = '"' + item.alasan + '"';

        const lampiranEl = document.getElementById('reviewModalLampiran');
        if (lampiranEl) {
            if (item.lampiran_url) {
                lampiranEl.innerHTML = `<a href="${item.lampiran_url}" target="_blank" class="btn-pill-action btn-pill-secondary" style="font-size:11.5px;display:inline-flex;align-items:center;gap:4px;">📎 Buka Berkas Lampiran</a>`;
            } else {
                lampiranEl.textContent = 'Tidak ada berkas lampiran';
            }
        }

        const radioTolak = document.getElementById('radioTolak');
        const radioSetujui = document.getElementById('radioSetujui');
        if (defaultDecision === 'ditolak' && radioTolak) {
            radioTolak.checked = true;
        } else if (radioSetujui) {
            radioSetujui.checked = true;
        }

        const catatanEl = document.getElementById('reviewCatatan');
        if (catatanEl) catatanEl.value = '';

        const modal = document.getElementById('approvalModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }
    }

    function closeApprovalModal() {
        const modal = document.getElementById('approvalModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }

    function openDetailModal(item) {
        document.getElementById('detailGuru').textContent = item.guru;
        document.getElementById('detailJenjang').textContent = item.jenjang;
        document.getElementById('detailJenis').textContent = item.jenis;
        document.getElementById('detailPeriode').textContent = item.periode;
        document.getElementById('detailDurasi').textContent = item.hari + ' hari';
        document.getElementById('detailDiajukan').textContent = item.diajukan;
        document.getElementById('detailAlasan').textContent = item.alasan;

        const statusEl = document.getElementById('detailStatus');
        if (item.status === 'disetujui') {
            statusEl.innerHTML = '<span class="pill-status pill-status-valid">✓ Disetujui</span>';
        } else if (item.status === 'ditolak') {
            statusEl.innerHTML = '<span class="pill-status pill-status-revisi">✗ Ditolak</span>';
        } else {
            statusEl.innerHTML = '<span class="pill-status pill-status-menunggu">⏳ Menunggu Persetujuan</span>';
        }

        document.getElementById('detailApprover').textContent = item.approver || '-';
        document.getElementById('detailWaktuApproval').textContent = item.disetujui_at || '-';

        const catatanBox = document.getElementById('detailCatatanBox');
        if (item.catatan) {
            document.getElementById('detailCatatan').textContent = item.catatan;
            catatanBox.style.display = 'block';
        } else {
            catatanBox.style.display = 'none';
        }

        const lampiranContent = document.getElementById('detailLampiranContent');
        if (item.lampiran_url) {
            lampiranContent.innerHTML = `<a href="${item.lampiran_url}" target="_blank" class="btn-forest-primary" style="padding:6px 14px;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">📎 Download / Buka Berkas Lampiran</a>`;
        } else {
            lampiranContent.innerHTML = '<span style="color:var(--text-subtle);">Tidak melampirkan berkas</span>';
        }

        const modal = document.getElementById('detailModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }
</script>
@endpush
