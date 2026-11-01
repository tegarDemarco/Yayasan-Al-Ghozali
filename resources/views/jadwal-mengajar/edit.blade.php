@extends('layouts.app')
@section('title','Edit Jadwal Mengajar')
@section('page-title','Edit Jadwal Mengajar')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Edit Jadwal Mengajar</h1></div>
    <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:720px;">
    <div class="card">
        <form method="POST" action="{{ route('jadwal-mengajar.update', $jadwalMengajar) }}">
            @csrf @method('PUT')
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Guru <span class="req">*</span></label>
                    <select name="guru_id" class="form-control" required>
                        @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('guru_id',$jadwalMengajar->guru_id) == $g->id ? 'selected' : '' }}>{{ $g->user->name }} ({{ $g->jenjang }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sekolah <span class="req">*</span></label>
                    <select name="sekolah_id" class="form-control" required>
                        @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}" {{ old('sekolah_id',$jadwalMengajar->sekolah_id) == $s->id ? 'selected' : '' }}>{{ $s->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran',$jadwalMengajar->mata_pelajaran) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span class="req">*</span></label>
                    <input type="text" name="kelas" class="form-control" value="{{ old('kelas',$jadwalMengajar->kelas) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hari <span class="req">*</span></label>
                    <select name="hari" class="form-control" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari',$jadwalMengajar->hari) === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan',$jadwalMengajar->ruangan) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span class="req">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', substr($jadwalMengajar->jam_mulai,0,5)) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai <span class="req">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', substr($jadwalMengajar->jam_selesai,0,5)) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="req">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran',$jadwalMengajar->tahun_ajaran) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester <span class="req">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil" {{ old('semester',$jadwalMengajar->semester) === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester',$jadwalMengajar->semester) === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;">
                    <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', $jadwalMengajar->is_aktif) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#16a34a;">
                    Jadwal Aktif
                </label>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
