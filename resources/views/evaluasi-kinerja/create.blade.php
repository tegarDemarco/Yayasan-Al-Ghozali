@extends('layouts.app')
@section('title','Buat Evaluasi Kinerja')
@section('page-title','Buat Evaluasi Kinerja')

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Buat Evaluasi Kinerja Guru</h1></div>
    <a href="{{ route('evaluasi-kinerja.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
    <div class="card">
        <form method="POST" action="{{ route('evaluasi-kinerja.store') }}" id="evaluasiForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Guru <span class="req">*</span></label>
                <select name="guru_id" class="form-control" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $g)
                    <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->user->name }} – {{ $g->spesialisasi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="req">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran','2025/2026') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester <span class="req">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:4px;">
                <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px;">Komponen Penilaian (0–100)</div>

                @foreach([
                    ['nilai_kinerja','Kinerja Umum','30%','Disiplin, tanggung jawab, loyalitas, kerjasama','green'],
                    ['nilai_kehadiran','Kehadiran & Ketepatan Waktu','25%','Tingkat kehadiran dan ketepatan waktu mengajar','blue'],
                    ['nilai_administrasi','Administrasi Pembelajaran','20%','Kelengkapan RPP, jurnal, penilaian','yellow'],
                    ['nilai_pembelajaran','Kualitas Pembelajaran','25%','Penguasaan materi, metode, interaksi siswa','purple'],
                ] as [$field, $label, $bobot, $desc, $color])
                <div style="background:var(--body-bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div>
                            <div style="font-size:13.5px;font-weight:700;">{{ $label }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $desc }}</div>
                        </div>
                        <span class="badge badge-{{ $color }}" style="font-size:12px;">Bobot {{ $bobot }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <input type="range" name="{{ $field }}" id="{{ $field }}" min="0" max="100" value="{{ old($field, 80) }}"
                            oninput="updateScore('{{ $field }}')" style="flex:1;accent-color:#16a34a;">
                        <div style="width:52px;text-align:center;">
                            <span id="{{ $field }}_val" style="font-size:20px;font-weight:800;color:var(--primary);">{{ old($field, 80) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="background:var(--primary-50);border:1px solid var(--primary-200);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div style="font-size:13.5px;font-weight:700;color:var(--primary-dark);">Estimasi Nilai Akhir</div>
                    <div style="font-size:24px;font-weight:800;color:var(--primary);" id="nilaiAkhirEstimate">–</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Evaluasi</label>
                <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan dan rekomendasi untuk guru...">{{ old('catatan') }}</textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Evaluasi</button>
                <a href="{{ route('evaluasi-kinerja.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <div class="card" style="position:sticky;top:calc(var(--header-height) + 20px);">
        <div class="card-title" style="margin-bottom:16px;">Panduan Predikat</div>
        @foreach([
            ['Sangat Baik','≥ 90','badge-green'],
            ['Baik','75 – 89','badge-blue'],
            ['Cukup','60 – 74','badge-yellow'],
            ['Kurang','< 60','badge-red'],
        ] as [$pred, $range, $cls])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed var(--border);">
            <span class="badge {{ $cls }}">{{ $pred }}</span>
            <span style="font-size:13px;font-weight:600;color:var(--text-secondary);">{{ $range }}</span>
        </div>
        @endforeach

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-size:12px;color:var(--text-muted);line-height:1.7;">
                <strong>Rumus Nilai Akhir:</strong><br>
                (Kinerja × 30%) + (Kehadiran × 25%) + (Administrasi × 20%) + (Pembelajaran × 25%)
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const bobots = { nilai_kinerja: 0.30, nilai_kehadiran: 0.25, nilai_administrasi: 0.20, nilai_pembelajaran: 0.25 };

function updateScore(field) {
    const val = document.getElementById(field).value;
    document.getElementById(field + '_val').textContent = val;
    document.getElementById(field + '_val').style.color = val >= 75 ? 'var(--primary)' : 'var(--red)';
    hitung();
}

function hitung() {
    let total = 0;
    for (const [field, bobot] of Object.entries(bobots)) {
        total += parseFloat(document.getElementById(field).value || 0) * bobot;
    }
    const el = document.getElementById('nilaiAkhirEstimate');
    el.textContent = total.toFixed(1);
    el.style.color = total >= 75 ? 'var(--primary)' : 'var(--red)';
}

hitung();
</script>
@endpush
