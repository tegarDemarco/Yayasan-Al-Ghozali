@extends('layouts.app')
@section('title','Jadwal Mengajar')
@section('page-title','Jadwal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Jadwal Mengajar</h1>
        <p>{{ auth()->user()->isGuru() ? 'Jadwal mengajar Anda' : 'Kelola jadwal mengajar guru' }}</p>
    </div>
    @if(!auth()->user()->isGuru())
    <a href="{{ route('jadwal-mengajar.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Jadwal
    </a>
    @endif
</div>

<div class="card">
    <form method="GET">
        <div class="filter-bar">
            <select name="hari" class="form-control" style="width:auto;">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                <option value="{{ $hari }}" {{ request('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->filled('hari'))
            <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Guru</th>@endif
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                    <th>Tahun Ajaran</th>
                    <th>Status</th>
                    @if(!auth()->user()->isGuru())<th>Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwals->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())
                    <td>
                        <div style="font-weight:600;">{{ $jadwal->guru->user->name ?? '-' }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);"><span class="jenjang-{{ strtolower($jadwal->guru->jenjang ?? '') }}">{{ $jadwal->guru->jenjang ?? '' }}</span></div>
                    </td>
                    @endif
                    <td><strong>{{ $jadwal->mata_pelajaran }}</strong></td>
                    <td>{{ $jadwal->kelas }}</td>
                    <td>
                        @php $hariColors = ['Senin'=>'badge-blue','Selasa'=>'badge-green','Rabu'=>'badge-yellow','Kamis'=>'badge-purple','Jumat'=>'badge-orange','Sabtu'=>'badge-gray']; @endphp
                        <span class="badge {{ $hariColors[$jadwal->hari] ?? 'badge-gray' }}">{{ $jadwal->hari }}</span>
                    </td>
                    <td style="white-space:nowrap;">{{ substr($jadwal->jam_mulai,0,5) }} – {{ substr($jadwal->jam_selesai,0,5) }}</td>
                    <td>{{ $jadwal->ruangan ?? '-' }}</td>
                    <td>{{ $jadwal->tahun_ajaran }} / {{ $jadwal->semester }}</td>
                    <td><span class="badge {{ $jadwal->is_aktif ? 'badge-green' : 'badge-gray' }}">{{ $jadwal->is_aktif ? 'Aktif' : 'Non-Aktif' }}</span></td>
                    @if(!auth()->user()->isGuru())
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('jadwal-mengajar.edit', $jadwal) }}" class="btn btn-warning btn-sm btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                            </a>
                            <form method="POST" action="{{ route('jadwal-mengajar.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="10"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg><h3>Belum ada jadwal mengajar</h3><p>Tambah jadwal baru untuk memulai</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jadwals->hasPages())
    <div class="pagination">
        <div class="pagination-info">Menampilkan {{ $jadwals->firstItem() }}–{{ $jadwals->lastItem() }} dari {{ $jadwals->total() }} data</div>
        <div class="pagination-links">
            @if(!$jadwals->onFirstPage())<a href="{{ $jadwals->previousPageUrl() }}" class="page-link">←</a>@endif
            @foreach($jadwals->getUrlRange(1,$jadwals->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page == $jadwals->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($jadwals->hasMorePages())<a href="{{ $jadwals->nextPageUrl() }}" class="page-link">→</a>@endif
        </div>
    </div>
    @endif
</div>
@endsection
