@extends('layouts.app')
@section('title','SK Tugas')
@section('page-title','SK Tugas')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Surat Keputusan (SK) Tugas</h1>
        <p>Pengelolaan SK pengangkatan, mutasi, dan penugasan guru</p>
    </div>
    @if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah() || auth()->user()->isPengurusYayasan())
    <a href="{{ route('sk-tugas.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Terbitkan SK
    </a>
    @endif
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No SK</th>
                    <th>Guru</th>
                    <th>Judul SK</th>
                    <th>Jenis</th>
                    <th>Tanggal SK</th>
                    <th>Berlaku</th>
                    <th>Diterbitkan Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jenisColors = [
                        'Pengangkatan'=>'badge-green','Pemberhentian'=>'badge-red','Mutasi'=>'badge-blue',
                        'Penghargaan'=>'badge-yellow','Sanksi'=>'badge-red','Tugas Tambahan'=>'badge-purple'
                    ];
                @endphp
                @forelse($skTugas as $sk)
                <tr>
                    <td>
                        <span style="font-family:monospace;font-size:12.5px;font-weight:600;color:var(--primary);">{{ $sk->no_sk }}</span>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $sk->guru->user->name ?? '-' }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);"><span class="jenjang-{{ strtolower($sk->guru->jenjang ?? '') }}">{{ $sk->guru->jenjang ?? '' }}</span></div>
                    </td>
                    <td>{{ \Str::limit($sk->judul_sk, 40) }}</td>
                    <td><span class="badge {{ $jenisColors[$sk->jenis_sk] ?? 'badge-gray' }}">{{ $sk->jenis_sk }}</span></td>
                    <td>{{ $sk->tanggal_sk->format('d/m/Y') }}</td>
                    <td>
                        @if($sk->berlaku_mulai)
                            {{ $sk->berlaku_mulai->format('d/m/Y') }}
                            @if($sk->berlaku_sampai) – {{ $sk->berlaku_sampai->format('d/m/Y') }} @endif
                        @else
                            <span style="color:var(--text-muted);">–</span>
                        @endif
                    </td>
                    <td>{{ $sk->penerbit->name ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('sk-tugas.show', $sk) }}" class="btn btn-secondary btn-sm btn-icon" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </a>
                            @if($sk->file_path)
                            <a href="{{ asset('storage/'.$sk->file_path) }}" target="_blank" class="btn btn-secondary btn-sm btn-icon" title="Download PDF">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            </a>
                            @endif
                            @if(!auth()->user()->isGuru())
                            <form method="POST" action="{{ route('sk-tugas.destroy', $sk) }}" onsubmit="return confirm('Hapus SK ini?')">
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
                <tr><td colspan="8">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" />
                        </svg>
                        <h3>Belum ada SK Tugas</h3>
                        <p>SK Tugas yang diterbitkan akan tampil di sini</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
