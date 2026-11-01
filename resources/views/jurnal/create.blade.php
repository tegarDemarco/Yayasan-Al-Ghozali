@extends('layouts.app')
@section('title','Isi Jurnal Mengajar')
@section('page-title','Isi Jurnal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Isi Jurnal Mengajar</h1></div>
    <a href="{{ route('jurnal.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:720px;">
    <div class="card">
        <form method="POST" action="{{ route('jurnal.store') }}">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal <span class="req">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                @if($jadwals->count())
                <div class="form-group">
                    <label class="form-label">Jadwal (opsional)</label>
                    <select name="jadwal_id" class="form-control" id="jadwalSelect" onchange="fillFromJadwal(this)">
                        <option value="">-- Pilih jadwal atau isi manual --</option>
                        @foreach($jadwals as $j)
                        <option value="{{ $j->id }}" data-mapel="{{ $j->mata_pelajaran }}" data-kelas="{{ $j->kelas }}">
                            {{ $j->hari }} – {{ $j->mata_pelajaran }} ({{ $j->kelas }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" id="mapelInput" class="form-control" value="{{ old('mata_pelajaran') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" id="kelasInput" class="form-control" value="{{ old('kelas') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Pertemuan Ke</label>
                    <input type="text" name="pertemuan_ke" class="form-control" value="{{ old('pertemuan_ke') }}" placeholder="Mis: 1, 2, dst">
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Siswa Hadir <span class="req">*</span></label>
                    <input type="number" name="jumlah_siswa_hadir" class="form-control" value="{{ old('jumlah_siswa_hadir', 0) }}" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Materi Pembelajaran <span class="req">*</span></label>
                <textarea name="materi" class="form-control" rows="4" placeholder="Tuliskan materi yang diajarkan..." required>{{ old('materi') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Kegiatan Pembelajaran</label>
                <textarea name="kegiatan" class="form-control" rows="3" placeholder="Metode dan kegiatan pembelajaran...">{{ old('kegiatan') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan, kendala, dll...">{{ old('catatan') }}</textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
                <a href="{{ route('jurnal.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function fillFromJadwal(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('mapelInput').value = opt.dataset.mapel || '';
        document.getElementById('kelasInput').value = opt.dataset.kelas || '';
    }
}
</script>
@endpush
