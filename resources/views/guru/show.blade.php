@extends('layouts.app')
@section('title','Detail Guru')
@section('page-title','Detail Guru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Detail Guru</h1>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('guru.edit', $guru) }}" class="btn btn-warning">Edit Data</a>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">
    <div class="card" style="text-align:center;">
        <div style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:32px;margin:0 auto 16px;">
            {{ strtoupper(substr($guru->user->name ?? '-', 0, 2)) }}
        </div>
        <div style="font-size:18px;font-weight:800;margin-bottom:4px;">{{ $guru->user->name }}</div>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">{{ $guru->user->email }}</div>
        <div style="margin-bottom:8px;"><span class="jenjang-{{ strtolower($guru->jenjang) }}">{{ $guru->jenjang }}</span></div>
        @if($guru->status === 'aktif')
            <span class="badge badge-green"><span class="status-dot green"></span>Aktif</span>
        @else
            <span class="badge badge-gray"><span class="status-dot gray"></span>{{ ucfirst($guru->status) }}</span>
        @endif

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);text-align:left;">
            @foreach([['NIP', $guru->nip ?? '-'], ['NUPTK', $guru->nuptk ?? '-'], ['Status Kepegawaian', $guru->status_kepegawaian], ['Pendidikan', $guru->pendidikan_terakhir ?? '-'], ['Bergabung', $guru->tanggal_bergabung?->format('d/m/Y') ?? '-']] as [$label, $val])
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed var(--border);font-size:12.5px;">
                <span style="color:var(--text-muted);">{{ $label }}</span>
                <span style="font-weight:600;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <div class="card-title">Jadwal Mengajar</div>
                <a href="{{ route('jadwal-mengajar.index') }}" style="font-size:12px;color:var(--primary);text-decoration:none;">Lihat Semua →</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($guru->jadwalMengajars as $jadwal)
                        <tr>
                            <td>{{ $jadwal->hari }}</td>
                            <td>{{ substr($jadwal->jam_mulai,0,5) }}–{{ substr($jadwal->jam_selesai,0,5) }}</td>
                            <td>{{ $jadwal->mata_pelajaran }}</td>
                            <td>{{ $jadwal->kelas }}</td>
                            <td><span class="badge {{ $jadwal->is_aktif ? 'badge-green' : 'badge-gray' }}">{{ $jadwal->is_aktif ? 'Aktif' : 'Non-Aktif' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Belum ada jadwal</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Presensi Terbaru</div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Tanggal</th><th>Status</th><th>Masuk</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @forelse($guru->presensiGurus as $p)
                        <tr>
                            <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                            <td>
                                @php $colors = ['hadir'=>'badge-green','izin'=>'badge-blue','sakit'=>'badge-yellow','alpha'=>'badge-red','cuti'=>'badge-purple']; @endphp
                                <span class="badge {{ $colors[$p->status] ?? 'badge-gray' }}">{{ ucfirst($p->status) }}</span>
                            </td>
                            <td>{{ $p->waktu_masuk ?? '-' }}</td>
                            <td>{{ $p->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">Belum ada data presensi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
