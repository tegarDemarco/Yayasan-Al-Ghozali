@extends('layouts.app')
@section('title', 'Tambah Jadwal Mengajar')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

@section('content')
<div class="page-header-row" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Tambah Jadwal Mengajar</h1>
        <p class="page-subtitle">Daftarkan alokasi jadwal mata pelajaran dan guru pengajar</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('jadwal-mengajar.index') }}" class="btn-pill-action btn-pill-secondary">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<div style="max-width: 840px;">
    <div class="surface-white-card" style="padding: 28px 32px;">
        <form method="POST" action="{{ route('jadwal-mengajar.store') }}">
            @csrf
            
            <div class="form-grid form-grid-2" style="margin-bottom: 16px;">
                <!-- Guru Pengajar -->
                <div class="form-group">
                    <label class="form-label" for="guru_id">Guru Pengajar <span style="color: #ef4444;">*</span></label>
                    <select name="guru_id" id="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                        <option value="{{ $g->id }}" 
                            data-sekolah-id="{{ $g->sekolah_id }}" 
                            data-jenjang="{{ $g->jenjang }}" 
                            {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->user->name }} ({{ $g->jenjang }} – {{ $g->spesialisasi }})
                        </option>
                        @endforeach
                    </select>
                    @error('guru_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sekolah / Jenjang -->
                <div class="form-group">
                    <label class="form-label" for="sekolah_id">Sekolah / Unit <span style="color: #ef4444;">*</span></label>
                    <select name="sekolah_id" id="sekolah_id" class="form-control" required onchange="filterGuruBySekolah(this.value)">
                        @if($sekolahs->count() > 1)
                            <option value="">-- Pilih Sekolah --</option>
                        @endif
                        @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}" 
                            data-jenjang="{{ $s->jenjang }}" 
                            {{ (string)old('sekolah_id', $defaultSekolahId ?? '') === (string)$s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }} ({{ $s->jenjang }})
                        </option>
                        @endforeach
                    </select>
                    @error('sekolah_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mata Pelajaran -->
                <div class="form-group">
                    <label class="form-label" for="mata_pelajaran">Mata Pelajaran <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" placeholder="Contoh: Matematika, Bahasa Indonesia" required>
                    @error('mata_pelajaran')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kelas -->
                <div class="form-group">
                    <label class="form-label" for="kelas">Kelas <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="kelas" id="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Contoh: VII-A, X-RPL 1" required>
                    @error('kelas')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hari -->
                <div class="form-group">
                    <label class="form-label" for="hari">Hari <span style="color: #ef4444;">*</span></label>
                    <select name="hari" id="hari" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ruangan -->
                <div class="form-group">
                    <label class="form-label" for="ruangan">Ruangan</label>
                    <input type="text" name="ruangan" id="ruangan" class="form-control" value="{{ old('ruangan') }}" placeholder="Contoh: R. Teori 02, Lab Komputer">
                    @error('ruangan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jam Mulai -->
                <div class="form-group">
                    <label class="form-label" for="jam_mulai">Jam Mulai <span style="color: #ef4444;">*</span></label>
                    <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                    @error('jam_mulai')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jam Selesai -->
                <div class="form-group">
                    <label class="form-label" for="jam_selesai">Jam Selesai <span style="color: #ef4444;">*</span></label>
                    <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                    @error('jam_selesai')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div class="form-group">
                    <label class="form-label" for="tahun_ajaran">Tahun Ajaran <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran', '2025/2026') }}" placeholder="2025/2026" required>
                    @error('tahun_ajaran')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Semester -->
                <div class="form-group">
                    <label class="form-label" for="semester">Semester <span style="color: #ef4444;">*</span></label>
                    <select name="semester" id="semester" class="form-control" required>
                        <option value="Ganjil" {{ old('semester', 'Ganjil') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('semester')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--card-border); padding-top: 20px; margin-top: 10px;">
                <a href="{{ route('jadwal-mengajar.index') }}" class="btn-pill-action btn-pill-secondary">Batal</a>
                <button type="submit" class="btn-forest-primary" style="padding: 9px 22px; font-size: 13.5px;">
                    <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                    <span>Simpan Jadwal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterGuruBySekolah(sekolahId) {
        const guruSelect = document.getElementById('guru_id');
        if (!guruSelect) return;
        
        const currentGuruVal = guruSelect.value;
        const options = guruSelect.querySelectorAll('option');

        let hasSelected = false;
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const optSekolahId = opt.getAttribute('data-sekolah-id');
            if (!sekolahId || optSekolahId === sekolahId) {
                opt.style.display = '';
                if (opt.value === currentGuruVal) {
                    hasSelected = true;
                }
            } else {
                opt.style.display = 'none';
            }
        });

        if (!hasSelected && currentGuruVal && sekolahId) {
            guruSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sekolahSelect = document.getElementById('sekolah_id');
        if (sekolahSelect && sekolahSelect.value) {
            filterGuruBySekolah(sekolahSelect.value);
        }
    });
</script>
@endsection
