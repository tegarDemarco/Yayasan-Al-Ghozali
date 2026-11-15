@extends('layouts.app') 

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tambah Evaluasi Kinerja Guru</h4>
        </div>
        <div class="card-body">
            
            {{-- Menampilkan Error Validasi jika inputan salah --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('evaluasi-kinerja.store') }}" method="POST">
                @csrf
                
                {{-- Dropdown Pilih Guru Dinamis dari Database --}}
                <div class="mb-3">
                    <label for="guru_id" class="form-label">Nama Guru</label>
                    <select name="guru_id" id="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option> 
                            {{-- Catatan: Ganti '$guru->nama' dengan nama kolom nama guru di tabel Anda jika berbeda (misal: 'nama_guru') --}}
                        @endforeach
                    </select>
                </div>

                {{-- Input Tahun Ajaran --}}
                <div class="mb-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran / Semester</label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026 / Ganjil" required value="{{ old('tahun_ajaran') }}">
                </div>

                <div class="row">
                    {{-- Input Nilai Kinerja --}}
                    <div class="col-md-6 mb-3">
                        <label for="kinerja" class="form-label">Nilai Kinerja</label>
                        <input type="number" name="kinerja" id="kinerja" class="form-control" min="0" max="100" placeholder="0-100" required value="{{ old('kinerja') }}">
                    </div>

                    {{-- Input Nilai Kehadiran --}}
                    <div class="col-md-6 mb-3">
                        <label for="kehadiran" class="form-label">Nilai Kehadiran</label>
                        <input type="number" name="kehadiran" id="kehadiran" class="form-control" min="0" max="100" placeholder="0-100" required value="{{ old('kehadiran') }}">
                    </div>
                </div>

                <div class="row">
                    {{-- Input Nilai Administrasi --}}
                    <div class="col-md-6 mb-3">
                        <label for="administrasi" class="form-label">Nilai Administrasi</label>
                        <input type="number" name="administrasi" id="administrasi" class="form-control" min="0" max="100" placeholder="0-100" required value="{{ old('administrasi') }}">
                    </div>

                    {{-- Input Nilai Pembelajaran --}}
                    <div class="col-md-6 mb-3">
                        <label for="pembelajaran" class="form-label">Nilai Pembelajaran</label>
                        <input type="number" name="pembelajaran" id="pembelajaran" class="form-control" min="0" max="100" placeholder="0-100" required value="{{ old('pembelajaran') }}">
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                    <a href="{{ route('evaluasi-kinerja.create') }}" class="btn btn-primary mb-3">
    + Tambah Evaluasi Kinerja
</a>
            </form>

        </div>
    </div>
</div>
@endsection