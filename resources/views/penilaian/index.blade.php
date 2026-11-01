@extends('layouts.app')
@section('title','Penilaian Siswa')
@section('page-title','Penilaian Siswa')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Penilaian Siswa</h1><p>Data nilai dan penilaian siswa</p></div>
    @if(auth()->user()->isGuru())
    <a href="{{ route('penilaian.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Nilai
    </a>
    @endif
</div>

<div class="card">
    <form method="GET" style="margin-bottom:16px;">
        <div class="filter-bar">
            <select name="jenis" class="form-control" style="width:auto;">
                <option value="">Semua Jenis</option>
                @foreach(['Ulangan Harian','UTS','UAS','Tugas','Praktik','Portofolio'] as $j)
                <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            <input type="text" name="kelas" class="form-control" style="width:160px;" placeholder="Filter kelas..." value="{{ request('kelas') }}">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if(!auth()->user()->isGuru())<th>Guru</th>@endif
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Jenis Penilaian</th>
                    <th>Nilai</th>
                    <th>Predikat</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penilaians as $p)
                @php
                    $predikat = $p->nilai >= 90 ? 'A' : ($p->nilai >= 80 ? 'B' : ($p->nilai >= 70 ? 'C' : ($p->nilai >= 60 ? 'D' : 'E')));
                    $predColors = ['A'=>'badge-green','B'=>'badge-blue','C'=>'badge-yellow','D'=>'badge-orange','E'=>'badge-red'];
                @endphp
                <tr>
                    <td>{{ $penilaians->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())<td>{{ $p->guru->user->name ?? '-' }}</td>@endif
                    <td><strong>{{ $p->nama_siswa }}</strong></td>
                    <td>{{ $p->nis ?? '-' }}</td>
                    <td>{{ $p->kelas }}</td>
                    <td>{{ $p->mata_pelajaran }}</td>
                    <td><span class="badge badge-blue">{{ $p->jenis_penilaian }}</span></td>
                    <td>
                        <span style="font-size:15px;font-weight:800;color:{{ $p->nilai >= 75 ? 'var(--primary)' : 'var(--red)' }};">{{ $p->nilai }}</span>
                    </td>
                    <td><span class="badge {{ $predColors[$predikat] }}">{{ $predikat }}</span></td>
                    <td>{{ $p->tanggal_penilaian->format('d/m/Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('penilaian.destroy', $p) }}" onsubmit="return confirm('Hapus data nilai?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg><h3>Belum ada data penilaian</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
