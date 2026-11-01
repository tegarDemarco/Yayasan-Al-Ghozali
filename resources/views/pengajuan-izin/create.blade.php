@extends('layouts.app')
@section('title','Buat Pengajuan Izin/Cuti')
@section('page-title','Buat Pengajuan Izin/Cuti')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Buat Pengajuan Izin / Cuti</h1></div>
    <a href="{{ route('pengajuan-izin.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="max-width:640px;">
    <div class="card">
        <form method="POST" action="{{ route('pengajuan-izin.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis Pengajuan <span class="req">*</span></label>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                    @foreach(['Izin','Cuti','Sakit','Tugas Luar','Dinas'] as $j)
                    <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;font-size:13.5px;font-weight:500;">
                        <input type="radio" name="jenis_pengajuan" value="{{ $j }}" {{ old('jenis_pengajuan') === $j ? 'checked' : '' }} required style="accent-color:#16a34a;">
                        {{ $j }}
                    </label>
                    @endforeach
                </div>
                @error('jenis_pengajuan')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span class="req">*</span></label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required onchange="hitungHari()">
                    @error('tanggal_mulai')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span class="req">*</span></label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required onchange="hitungHari()">
                    @error('tanggal_selesai')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="hitungHariInfo" style="background:var(--primary-50);border:1px solid var(--primary-200);border-radius:var(--radius-sm);padding:10px 14px;font-size:13.5px;color:var(--primary-dark);margin-bottom:18px;display:none;">
                📅 Durasi: <strong id="jumlahHariText">0</strong> hari
            </div>

            <div class="form-group">
                <label class="form-label">Alasan <span class="req">*</span></label>
                <textarea name="alasan" class="form-control" rows="4" placeholder="Jelaskan alasan pengajuan izin/cuti Anda..." required minlength="10">{{ old('alasan') }}</textarea>
                @error('alasan')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Lampiran <span style="color:var(--text-muted);font-weight:400;">(Opsional – PDF, JPG, PNG, maks. 2MB)</span></label>
                <input type="file" name="lampiran" class="form-control" accept=".pdf,.jpg,.png">
                @error('lampiran')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="background:var(--accent-light);border:1px solid #fde68a;border-radius:var(--radius-sm);padding:12px 14px;font-size:13px;color:#92400e;margin-bottom:20px;">
                ⚠️ Pengajuan akan dikirim ke Kepala Sekolah untuk mendapatkan persetujuan.
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                <a href="{{ route('pengajuan-izin.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hitungHari() {
    const mulai = new Date(document.getElementById('tanggal_mulai').value);
    const selesai = new Date(document.getElementById('tanggal_selesai').value);
    if (mulai && selesai && selesai >= mulai) {
        const diff = Math.round((selesai - mulai) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('jumlahHariText').textContent = diff;
        document.getElementById('hitungHariInfo').style.display = 'block';
    } else {
        document.getElementById('hitungHariInfo').style.display = 'none';
    }
}
hitungHari();
</script>
@endpush
