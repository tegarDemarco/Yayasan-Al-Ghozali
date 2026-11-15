@extends('layouts.app')

@php
    $user = auth()->user();
    $isKepsek = $user->isKepalaSekolah();
    $isDisetujui = ($rpp->status_validasi === 'disetujui');
    $isDitolak   = ($rpp->status_validasi === 'ditolak');
    $isMenunggu  = ($rpp->status_validasi === 'menunggu');

    $guruUser  = $rpp->guru?->user;
    $guruName  = $guruUser?->name ?? 'Guru';
    $guruNip   = $rpp->guru?->nip ?? '-';
    $guruFoto  = $guruUser?->foto;
    $guruMapel = $rpp->guru?->spesialisasi ?? $rpp->mata_pelajaran;

    $initials = collect(explode(' ', $guruName))
        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
        ->take(2)
        ->join('');

    $ext      = $rpp->file_path ? strtolower(pathinfo($rpp->file_path, PATHINFO_EXTENSION)) : null;
    $fileUrl  = $rpp->file_path ? asset('storage/' . $rpp->file_path) : null;
    $isPdf    = $ext === 'pdf';
@endphp

@section('title', 'Detail RPP / Modul Ajar')
@section('page-title', 'Detail Dokumen')

@section('content')

{{-- Back + Header --}}
<div class="page-header-block" style="margin-bottom:8px;">
    <div>
        <div class="page-eyebrow">
            <a href="{{ route('rpp.index') }}" style="color:var(--forest-700);text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-weight:600;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                Kembali
            </a>
        </div>
        <h1 class="page-main-title">Detail RPP / Modul Ajar</h1>
        <div class="page-sub-caption">Informasi lengkap dokumen dan berkas yang diunggah</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- ── Left column ─────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Document Info Card --}}
        <div class="surface-white-card" style="padding:24px;">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:20px;">
                <div style="width:48px;height:48px;border-radius:10px;background:var(--forest-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="file-text" style="width:24px;height:24px;color:var(--forest-700);"></i>
                </div>
                <div>
                    <div style="font-size:18px;font-weight:700;color:var(--text-heading);line-height:1.3;">{{ $rpp->judul }}</div>
                    <div style="font-size:12.5px;color:var(--text-muted);margin-top:3px;">
                        {{ $rpp->tipe }} &middot; Kelas {{ $rpp->kelas }} &middot; {{ $rpp->semester }}
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px 24px;">
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Mata Pelajaran</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->mata_pelajaran }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Tahun Ajaran</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->tahun_ajaran }} / {{ $rpp->semester }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Kelas</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->kelas }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Jenis Dokumen</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->tipe }}</div>
                </div>
                @if($rpp->pertemuan_ke)
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pertemuan Ke</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->pertemuan_ke }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Diunggah</div>
                    <div style="font-weight:600;color:var(--text-heading);">{{ $rpp->created_at?->format('d M Y, H:i') ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- File / Dokumen Card --}}
        <div class="surface-white-card" style="padding:24px;">
            <div style="font-size:13px;font-weight:700;color:var(--text-heading);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <i data-lucide="paperclip" style="width:16px;height:16px;color:var(--forest-600);"></i>
                Berkas Dokumen
            </div>

            @if($fileUrl)
                {{-- File meta row --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--surface-bg);border-radius:10px;border:1px solid var(--border-light);margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:8px;background:{{ $isPdf ? '#fee2e2' : '#dbeafe' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-lucide="{{ $isPdf ? 'file-type-2' : 'file-type' }}" style="width:20px;height:20px;color:{{ $isPdf ? '#dc2626' : '#2563eb' }};"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:13px;color:var(--text-heading);">{{ basename($rpp->file_path) }}</div>
                            <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;">{{ strtoupper($ext) }} &middot; Berkas terlampir</div>
                        </div>
                    </div>
                    <a href="{{ $fileUrl }}" target="_blank" class="btn-forest-primary" style="font-size:12.5px;padding:8px 14px;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
                        <i data-lucide="external-link" style="width:14px;height:14px;"></i>
                        Buka Dokumen
                    </a>
                </div>

                {{-- PDF embed --}}
                @if($isPdf)
                <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border-light);">
                    <div style="padding:10px 14px;background:var(--surface-bg);border-bottom:1px solid var(--border-light);font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                        <i data-lucide="eye" style="width:14px;height:14px;"></i>
                        Pratinjau PDF
                    </div>
                    <iframe src="{{ $fileUrl }}" style="width:100%;height:520px;border:none;" title="Pratinjau {{ $rpp->judul }}"></iframe>
                </div>
                @else
                <div style="padding:28px;text-align:center;background:var(--surface-bg);border-radius:10px;border:1px dashed var(--border-light);">
                    <i data-lucide="file" style="width:40px;height:40px;color:var(--text-subtle);margin-bottom:10px;"></i>
                    <div style="font-size:13px;color:var(--text-muted);">Pratinjau tidak tersedia untuk format <strong>{{ strtoupper($ext) }}</strong>.<br>Klik <strong>Buka Dokumen</strong> untuk membuka file.</div>
                </div>
                @endif
            @else
                <div style="padding:32px;text-align:center;color:var(--text-muted);">
                    <i data-lucide="file-x" style="width:40px;height:40px;color:var(--text-subtle);margin-bottom:10px;"></i>
                    <div>Berkas dokumen tidak ditemukan.</div>
                </div>
            @endif
        </div>

    </div>

    {{-- ── Right column ─────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Status Card --}}
        <div class="surface-white-card" style="padding:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Status Validasi</div>

            @if($isMenunggu)
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef9c3;border-radius:8px;border:1px solid #fde047;">
                    <i data-lucide="clock" style="width:16px;height:16px;color:#ca8a04;"></i>
                    <span style="font-weight:600;color:#854d0e;font-size:13px;">Menunggu Validasi</span>
                </div>
            @elseif($isDisetujui)
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#dcfce7;border-radius:8px;border:1px solid #86efac;">
                    <i data-lucide="check-circle" style="width:16px;height:16px;color:#16a34a;"></i>
                    <span style="font-weight:600;color:#166534;font-size:13px;">Valid / Disetujui</span>
                </div>
            @elseif($isDitolak)
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fee2e2;border-radius:8px;border:1px solid #fca5a5;">
                    <i data-lucide="x-circle" style="width:16px;height:16px;color:#dc2626;"></i>
                    <span style="font-weight:600;color:#991b1b;font-size:13px;">Perlu Revisi</span>
                </div>
            @endif

            @if($rpp->divalidasi_at)
            <div style="margin-top:12px;font-size:12px;color:var(--text-muted);">
                <span>Dievaluasi: {{ $rpp->divalidasi_at->format('d M Y, H:i') }}</span>
                @if($rpp->validator)
                <div style="margin-top:2px;">Oleh: <span style="font-weight:600;color:var(--text-heading);">{{ $rpp->validator->name }}</span></div>
                @endif
            </div>
            @endif

            @if($rpp->catatan_validasi)
            <div style="margin-top:14px;padding:12px;background:#fffbeb;border-radius:8px;border-left:3px solid #f59e0b;">
                <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Catatan Kepala Sekolah</div>
                <div style="font-size:13px;color:#78350f;line-height:1.5;">{{ $rpp->catatan_validasi }}</div>
            </div>
            @endif

            {{-- Kepala Sekolah action buttons (if menunggu) --}}
            @if($isKepsek && $isMenunggu)
            <div style="margin-top:16px;border-top:1px solid var(--border-light);padding-top:14px;">
                <div style="font-size:11.5px;color:var(--text-muted);margin-bottom:10px;">Ambil tindakan untuk dokumen ini:</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <form method="POST" action="{{ route('rpp.validasi', $rpp) }}">
                        @csrf
                        <input type="hidden" name="status_validasi" value="disetujui">
                        <button type="submit" class="btn-forest-primary" style="width:100%;justify-content:center;">
                            <i data-lucide="check-circle" style="width:15px;height:15px;"></i>
                            Validasi Dokumen
                        </button>
                    </form>
                    <button type="button" class="btn-pill-action btn-pill-danger" style="width:100%;justify-content:center;padding:9px 16px;"
                            onclick="openRevisiModal({{ $rpp->id }}, '{{ addslashes($rpp->judul) }}')">
                        <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                        Kirim Revisi
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- Guru Card --}}
        <div class="surface-white-card" style="padding:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Pengunggah</div>
            <div style="display:flex;align-items:center;gap:12px;">
                @if($guruFoto)
                    <img src="{{ asset('storage/'.$guruFoto) }}"
                         alt="{{ $guruName }}"
                         style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--forest-200);"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div style="display:none;width:52px;height:52px;border-radius:50%;background:var(--forest-500);color:#fff;font-weight:700;font-size:17px;align-items:center;justify-content:center;flex-shrink:0;">
                        {{ $initials }}
                    </div>
                @else
                    <div style="width:52px;height:52px;border-radius:50%;background:var(--forest-500);color:#fff;font-weight:700;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        {{ $initials }}
                    </div>
                @endif
                <div>
                    <div style="font-weight:700;color:var(--text-heading);font-size:13.5px;">{{ $guruName }}</div>
                    <div style="font-size:11.5px;color:var(--text-muted);">{{ $guruMapel }}</div>
                    <div style="font-size:11px;color:var(--text-subtle);margin-top:1px;">NIP {{ $guruNip }}</div>
                </div>
            </div>
            @if($rpp->sekolah)
            <div style="margin-top:12px;padding:8px 12px;background:var(--surface-bg);border-radius:7px;font-size:12px;color:var(--text-muted);">
                <i data-lucide="building-2" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>
                {{ $rpp->sekolah->nama }}
            </div>
            @endif
        </div>

        {{-- Back button --}}
        <a href="{{ route('rpp.index') }}" class="btn-pill-action btn-pill-secondary" style="display:flex;align-items:center;gap:6px;justify-content:center;padding:10px 16px;">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
            Kembali ke Daftar
        </a>

    </div>
</div>

{{-- Revisi Modal --}}
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

