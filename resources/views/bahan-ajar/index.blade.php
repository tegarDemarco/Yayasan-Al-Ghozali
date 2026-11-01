@extends('layouts.app')
@section('title','Bahan Ajar')
@section('page-title','Bahan Ajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Upload Bahan Ajar</h1><p>Materi dan sumber belajar untuk siswa</p></div>
    @if(auth()->user()->isGuru())
    <a href="{{ route('bahan-ajar.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
        Upload Bahan Ajar
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
                    <th>Judul</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Tipe File</th>
                    <th>Ukuran</th>
                    <th>Tanggal Upload</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahanAjars as $b)
                <tr>
                    <td>{{ $bahanAjars->firstItem() + $loop->index }}</td>
                    @if(!auth()->user()->isGuru())<td><strong>{{ $b->guru->user->name ?? '-' }}</strong></td>@endif
                    <td>{{ \Str::limit($b->judul, 35) }}</td>
                    <td>{{ $b->mata_pelajaran }}</td>
                    <td>{{ $b->kelas }}</td>
                    <td>
                        @php $extColors = ['pdf'=>'badge-red','doc'=>'badge-blue','docx'=>'badge-blue','ppt'=>'badge-orange','pptx'=>'badge-orange','jpg'=>'badge-green','png'=>'badge-green','zip'=>'badge-gray']; @endphp
                        <span class="badge {{ $extColors[strtolower($b->tipe_file ?? '')] ?? 'badge-gray' }}">{{ strtoupper($b->tipe_file ?? 'FILE') }}</span>
                    </td>
                    <td>{{ $b->ukuran_file ? number_format($b->ukuran_file/1024, 0).' KB' : '-' }}</td>
                    <td>{{ $b->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ asset('storage/'.$b->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download
                            </a>
                            @if(auth()->user()->isGuru())
                            <form method="POST" action="{{ route('bahan-ajar.destroy', $b) }}" onsubmit="return confirm('Hapus?')">
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
                <tr><td colspan="9"><div class="empty-state"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg><h3>Belum ada bahan ajar</h3></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
