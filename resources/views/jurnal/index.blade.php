@extends('layouts.app')
@section('title','Jurnal Mengajar')
@section('page-title','Jurnal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Jurnal Mengajar</h1><p>Catatan kegiatan belajar mengajar harian</p></div>
    @if(auth()->user()->isGuru())
    <a href="{{ route('jurnal.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Jurnal
    </a>
    @endif
</div>
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Guru</th>@endif
                    <th>Tanggal</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Pertemuan</th>
                    <th>Materi</th>
                    <th>Siswa Hadir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jurnals as $j)
                <tr>
                    <td>{{ $jurnals->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())<td><strong>{{ $j->guru->user->name ?? '-' }}</strong></td>@endif
                    <td>{{ $j->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $j->mata_pelajaran }}</td>
                    <td>{{ $j->kelas }}</td>
                    <td>{{ $j->pertemuan_ke ?? '-' }}</td>
                    <td>{{ \Str::limit($j->materi, 40) }}</td>
                    <td>{{ $j->jumlah_siswa_hadir }} siswa</td>
                    <td><span class="badge {{ $j->status === 'terkirim' ? 'badge-green' : 'badge-yellow' }}">{{ ucfirst($j->status) }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('jurnal.show', $j) }}" class="btn btn-secondary btn-sm btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </a>
                            @if(auth()->user()->isGuru())
                            <form method="POST" action="{{ route('jurnal.destroy', $j) }}" onsubmit="return confirm('Hapus jurnal?')">
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
                <tr><td colspan="10"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg><h3>Belum ada jurnal mengajar</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jurnals->hasPages())
    <div class="pagination">
        <div class="pagination-info">{{ $jurnals->firstItem() }}–{{ $jurnals->lastItem() }} dari {{ $jurnals->total() }}</div>
        <div class="pagination-links">
            @if(!$jurnals->onFirstPage())<a href="{{ $jurnals->previousPageUrl() }}" class="page-link">←</a>@endif
            @foreach($jurnals->getUrlRange(1,$jurnals->lastPage()) as $page => $url)<a href="{{ $url }}" class="page-link {{ $page==$jurnals->currentPage()?'active':'' }}">{{ $page }}</a>@endforeach
            @if($jurnals->hasMorePages())<a href="{{ $jurnals->nextPageUrl() }}" class="page-link">→</a>@endif
        </div>
    </div>
    @endif
</div>
@endsection
