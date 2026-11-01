@extends('layouts.app')
@section('title','Presensi Guru')
@section('page-title','Presensi Guru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Presensi Guru</h1>
        <p>Rekap kehadiran guru harian</p>
    </div>
    <a href="{{ route('presensi.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        {{ auth()->user()->isGuru() ? 'Isi Presensi' : 'Tambah Presensi' }}
    </a>
</div>

<div style="margin-bottom:20px;">
    <form method="GET">
        <div class="filter-bar">
            <div class="form-group" style="margin:0;">
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <button type="submit" class="btn btn-secondary">Tampilkan</button>
        </div>
    </form>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    @foreach([['Hadir','hadir','green','icon-green'],['Izin','izin','blue','icon-blue'],['Sakit','sakit','yellow','icon-yellow'],['Alpha','alpha','red','icon-red']] as [$label,$key,$color,$iconCls])
    <div class="stat-card accent-{{ $color }}">
        <div class="stat-icon {{ $iconCls }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $ringkasan[$key] ?? 0 }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Data Presensi – {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Nama Guru</th><th>Jenjang</th>@endif
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Waktu Masuk</th>
                    <th>Keterangan</th>
                    @if(!auth()->user()->isGuru())<th>Dicatat Oleh</th>@endif
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $statusColors = ['hadir'=>'badge-green','izin'=>'badge-blue','sakit'=>'badge-yellow','alpha'=>'badge-red','cuti'=>'badge-purple']; @endphp
                @forelse($presensi as $p)
                <tr>
                    <td>{{ $presensi->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())
                    <td><strong>{{ $p->guru->user->name ?? '-' }}</strong></td>
                    <td><span class="jenjang-{{ strtolower($p->guru->jenjang ?? '') }}">{{ $p->guru->jenjang ?? '' }}</span></td>
                    @endif
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $statusColors[$p->status] ?? 'badge-gray' }}">{{ ucfirst($p->status) }}</span></td>
                    <td>{{ $p->waktu_masuk ?? '-' }}</td>
                    <td>{{ $p->keterangan ? \Str::limit($p->keterangan, 30) : '-' }}</td>
                    @if(!auth()->user()->isGuru())
                    <td>{{ $p->pencatat?->name ?? 'Sistem' }}</td>
                    @endif
                    <td>
                        <form method="POST" action="{{ route('presensi.destroy', $p) }}" onsubmit="return confirm('Hapus data presensi?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><h3>Belum ada data presensi</h3><p>pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
