@extends('layouts.app')
@section('title','Data Guru')
@section('page-title','Data Guru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Data Guru</h1>
        <p>Kelola data seluruh guru di yayasan</p>
    </div>
    @can('create', App\Models\Guru::class)
    <a href="{{ route('guru.create') }}" class="btn btn-primary" id="btn-tambah-guru">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Guru
    </a>
    @endcan
</div>

<div class="card">
    <form method="GET" action="{{ route('guru.index') }}">
        <div class="filter-bar">
            <div class="search-box">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" class="form-control" placeholder="Cari nama guru..." value="{{ request('search') }}">
            </div>
            <select name="jenjang" class="form-control" style="width:auto;">
                <option value="">Semua Jenjang</option>
                <option value="SD" {{ request('jenjang') === 'SD' ? 'selected' : '' }}>SD</option>
                <option value="MTs" {{ request('jenjang') === 'MTs' ? 'selected' : '' }}>MTs</option>
                <option value="SMK" {{ request('jenjang') === 'SMK' ? 'selected' : '' }}>SMK</option>
            </select>
            <select name="status" class="form-control" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
            </select>
            <button type="submit" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                Filter
            </button>
            @if(request()->anyFilled(['search','jenjang','status']))
            <a href="{{ route('guru.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Guru</th>
                    <th>NIP</th>
                    <th>Mata Pelajaran</th>
                    <th>Jenjang</th>
                    <th>Status Kepegawaian</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $guru)
                <tr>
                    <td>{{ $gurus->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($guru->user->name ?? '-', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $guru->user->name ?? '-' }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $guru->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $guru->nip ?? '-' }}</td>
                    <td>{{ $guru->spesialisasi }}</td>
                    <td><span class="jenjang-{{ strtolower($guru->jenjang) }}">{{ $guru->jenjang }}</span></td>
                    <td>
                        @php
                            $kepColors = ['PNS'=>'badge-blue','GTY'=>'badge-green','GTT'=>'badge-yellow','Honorer'=>'badge-gray'];
                        @endphp
                        <span class="badge {{ $kepColors[$guru->status_kepegawaian] ?? 'badge-gray' }}">{{ $guru->status_kepegawaian }}</span>
                    </td>
                    <td>
                        @if($guru->status === 'aktif')
                            <span class="badge badge-green"><span class="status-dot green"></span>Aktif</span>
                        @elseif($guru->status === 'cuti')
                            <span class="badge badge-yellow"><span class="status-dot yellow"></span>Cuti</span>
                        @else
                            <span class="badge badge-gray"><span class="status-dot gray"></span>Non-Aktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <a href="{{ route('guru.show', $guru) }}" class="btn btn-secondary btn-sm btn-icon" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </a>
                            <a href="{{ route('guru.edit', $guru) }}" class="btn btn-warning btn-sm btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                            </a>
                            <form method="POST" action="{{ route('guru.destroy', $guru) }}" onsubmit="return confirm('Hapus guru ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        <h3>Belum ada data guru</h3>
                        <p>Tambahkan guru baru untuk memulai</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($gurus->hasPages())
    <div class="pagination">
        <div class="pagination-info">Menampilkan {{ $gurus->firstItem() }}–{{ $gurus->lastItem() }} dari {{ $gurus->total() }} data</div>
        <div class="pagination-links">
            @if($gurus->onFirstPage())
                <span class="page-link disabled">←</span>
            @else
                <a href="{{ $gurus->previousPageUrl() }}" class="page-link">←</a>
            @endif
            @foreach($gurus->getUrlRange(max(1,$gurus->currentPage()-2), min($gurus->lastPage(),$gurus->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $gurus->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($gurus->hasMorePages())
                <a href="{{ $gurus->nextPageUrl() }}" class="page-link">→</a>
            @else
                <span class="page-link disabled">→</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
