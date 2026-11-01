@extends('layouts.app')
@section('title','Pengajuan Izin & Cuti')
@section('page-title','Pengajuan Izin & Cuti')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Pengajuan Izin & Cuti</h1>
        <p>{{ auth()->user()->isGuru() ? 'Kelola pengajuan izin dan cuti Anda' : 'Daftar pengajuan izin & cuti guru' }}</p>
    </div>
    @if(auth()->user()->isGuru())
    <a href="{{ route('pengajuan-izin.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Buat Pengajuan
    </a>
    @endif
</div>

<div class="card">
    <form method="GET" style="margin-bottom:16px;">
        <div class="filter-bar">
            <select name="status" class="form-control" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status')==='menunggu'?'selected':'' }}>Menunggu</option>
                <option value="disetujui" {{ request('status')==='disetujui'?'selected':'' }}>Disetujui</option>
                <option value="ditolak" {{ request('status')==='ditolak'?'selected':'' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('status'))
            <a href="{{ route('pengajuan-izin.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Guru</th>@endif
                    <th>Jenis</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th style="text-align:center;">Hari</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jenisColors = ['Izin'=>'badge-blue','Cuti'=>'badge-purple','Sakit'=>'badge-yellow','Tugas Luar'=>'badge-orange','Dinas'=>'badge-green'];
                    $statusColors = ['menunggu'=>'badge-yellow','disetujui'=>'badge-green','ditolak'=>'badge-red'];
                @endphp
                @forelse($pengajuans as $p)
                <tr>
                    <td>{{ $pengajuans->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())
                    <td>
                        <div style="font-weight:600;">{{ $p->guru->user->name ?? '-' }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);"><span class="jenjang-{{ strtolower($p->guru->jenjang ?? '') }}">{{ $p->guru->jenjang ?? '' }}</span></div>
                    </td>
                    @endif
                    <td><span class="badge {{ $jenisColors[$p->jenis_pengajuan] ?? 'badge-gray' }}">{{ $p->jenis_pengajuan }}</span></td>
                    <td>{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                    <td style="text-align:center;font-weight:700;">{{ $p->jumlah_hari }} hari</td>
                    <td>{{ \Str::limit($p->alasan, 35) }}</td>
                    <td>
                        <span class="badge {{ $statusColors[$p->status_approval] ?? 'badge-gray' }}">
                            {{ match($p->status_approval) {
                                'menunggu' => '⏳ Menunggu',
                                'disetujui' => '✓ Disetujui',
                                'ditolak' => '✗ Ditolak',
                                default => ucfirst($p->status_approval)
                            } }}
                        </span>
                    </td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if(($p->status_approval === 'menunggu') && (auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah()))
                            <button class="btn btn-primary btn-sm" onclick="openApprovalModal({{ $p->id }})">Review</button>
                            @endif

                            @if($p->lampiran)
                            <a href="{{ asset('storage/'.$p->lampiran) }}" target="_blank" class="btn btn-secondary btn-sm btn-icon" title="Lihat Lampiran">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                            </a>
                            @endif

                            @if(auth()->user()->isGuru() && $p->status_approval === 'menunggu')
                            <form method="POST" action="{{ route('pengajuan-izin.destroy', $p) }}" onsubmit="return confirm('Batalkan pengajuan?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                            @endif
                        </div>

                        @if($p->status_approval !== 'menunggu' && $p->catatan_approval)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">💬 {{ \Str::limit($p->catatan_approval, 30) }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                        <h3>Belum ada pengajuan izin</h3>
                        <p>{{ auth()->user()->isGuru() ? 'Klik "Buat Pengajuan" untuk mengajukan izin atau cuti' : 'Belum ada guru yang mengajukan izin' }}</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuans->hasPages())
    <div class="pagination">
        <div class="pagination-info">{{ $pengajuans->firstItem() }}–{{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }}</div>
        <div class="pagination-links">
            @if(!$pengajuans->onFirstPage())<a href="{{ $pengajuans->previousPageUrl() }}" class="page-link">←</a>@endif
            @foreach($pengajuans->getUrlRange(1,$pengajuans->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page==$pengajuans->currentPage()?'active':'' }}">{{ $page }}</a>
            @endforeach
            @if($pengajuans->hasMorePages())<a href="{{ $pengajuans->nextPageUrl() }}" class="page-link">→</a>@endif
        </div>
    </div>
    @endif
</div>

{{-- Approval Modal --}}
@if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
<div class="modal-overlay" id="approvalModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Review Pengajuan Izin / Cuti</div>
            <button class="modal-close" onclick="document.getElementById('approvalModal').classList.remove('active')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form id="approvalForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Keputusan <span class="req">*</span></label>
                <div style="display:flex;gap:10px;">
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;" onclick="this.style.borderColor='#16a34a'">
                        <input type="radio" name="status_approval" value="disetujui" required style="accent-color:#16a34a;">
                        <span style="font-weight:700;color:var(--primary);">✓ Setujui</span>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;" onclick="this.style.borderColor='#ef4444'">
                        <input type="radio" name="status_approval" value="ditolak" style="accent-color:#ef4444;">
                        <span style="font-weight:700;color:var(--red);">✗ Tolak</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan_approval" class="form-control" rows="3" placeholder="Catatan keputusan (opsional)..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('approvalModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openApprovalModal(id) {
    document.getElementById('approvalForm').action = '/pengajuan-izin/' + id + '/approve';
    document.getElementById('approvalModal').classList.add('active');
}
</script>
@endpush
