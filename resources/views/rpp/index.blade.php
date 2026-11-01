@extends('layouts.app')
@section('title','RPP & Modul Ajar')
@section('page-title','RPP & Modul Ajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>RPP & Modul Ajar</h1><p>Dokumen perencanaan pembelajaran</p></div>
    @if(auth()->user()->isGuru())
    <a href="{{ route('rpp.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
        Upload RPP / Modul Ajar
    </a>
    @endif
</div>

<div class="card">
    <form method="GET" style="margin-bottom:16px;">
        <div class="filter-bar">
            <select name="status" class="form-control" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status')==='menunggu'?'selected':'' }}>Menunggu Validasi</option>
                <option value="disetujui" {{ request('status')==='disetujui'?'selected':'' }}>Disetujui</option>
                <option value="ditolak" {{ request('status')==='ditolak'?'selected':'' }}>Ditolak</option>
            </select>
            <select name="tipe" class="form-control" style="width:auto;">
                <option value="">Semua Tipe</option>
                <option value="RPP" {{ request('tipe')==='RPP'?'selected':'' }}>RPP</option>
                <option value="Modul Ajar" {{ request('tipe')==='Modul Ajar'?'selected':'' }}>Modul Ajar</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Guru</th>@endif
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Status Validasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $valColors = ['menunggu'=>'badge-yellow','disetujui'=>'badge-green','ditolak'=>'badge-red']; @endphp
                @forelse($rpps as $rpp)
                <tr>
                    <td>{{ $rpps->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())<td><strong>{{ $rpp->guru->user->name ?? '-' }}</strong></td>@endif
                    <td>{{ \Str::limit($rpp->judul, 35) }}</td>
                    <td><span class="badge {{ $rpp->tipe === 'RPP' ? 'badge-blue' : 'badge-purple' }}">{{ $rpp->tipe }}</span></td>
                    <td>{{ $rpp->mata_pelajaran }}</td>
                    <td>{{ $rpp->kelas }}</td>
                    <td>{{ $rpp->semester }}</td>
                    <td><span class="badge {{ $valColors[$rpp->status_validasi] ?? 'badge-gray' }}">{{ ucfirst($rpp->status_validasi) }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($rpp->file_path)
                            <a href="{{ asset('storage/'.$rpp->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download
                            </a>
                            @endif

                            @if(auth()->user()->isKepalaSekolah() && $rpp->status_validasi === 'menunggu')
                            <button type="button" class="btn btn-primary btn-sm" onclick="openValidasiModal({{ $rpp->id }})">Validasi</button>
                            @endif

                            @if(auth()->user()->isGuru())
                            <form method="POST" action="{{ route('rpp.destroy', $rpp) }}" onsubmit="return confirm('Hapus dokumen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg><h3>Belum ada RPP/Modul Ajar</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Validasi Modal --}}
@if(auth()->user()->isKepalaSekolah())
<div class="modal-overlay" id="validasiModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Validasi RPP / Modul Ajar</div>
            <button class="modal-close" onclick="closeValidasiModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form id="validasiForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Keputusan <span class="req">*</span></label>
                <div style="display:flex;gap:10px;">
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;">
                        <input type="radio" name="status_validasi" value="disetujui" required> <span style="font-weight:600;color:var(--primary);">✓ Setujui</span>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;">
                        <input type="radio" name="status_validasi" value="ditolak"> <span style="font-weight:600;color:var(--red);">✗ Tolak</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan_validasi" class="form-control" rows="3" placeholder="Catatan validasi (opsional)..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeValidasiModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openValidasiModal(rppId) {
    document.getElementById('validasiForm').action = '/rpp/' + rppId + '/validasi';
    document.getElementById('validasiModal').classList.add('active');
}
function closeValidasiModal() {
    document.getElementById('validasiModal').classList.remove('active');
}
</script>
@endpush
