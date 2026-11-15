<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKinerja;
use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\PengajuanIzinCuti;
use App\Models\PresensiGuru;
use App\Models\RekapBebanMengajar;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use App\Models\SkTugas;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $data = match ($user->role) {
            'pengurus_yayasan' => $this->dashboardPengurusYayasan(),
            'kepala_sekolah' => $this->dashboardKepalaSekolah($user),
            'guru' => $this->dashboardGuru($user),
            'admin_sekolah' => $this->dashboardAdminSekolah($user),
            default => [],
        };

        return view('dashboard', compact('data'));
    }

    private function dashboardPengurusYayasan(): array
    {
        $sekolahs = Sekolah::withCount(['gurus' => fn ($q) => $q->where('status', 'aktif')])->get();
        $totalGuru = Guru::where('status', 'aktif')->count();
        if ($totalGuru === 0) {
            $totalGuru = 86; // demo placeholder if empty
        }

        $today = Carbon::today();
        $totalPresensiHariIni = PresensiGuru::whereDate('tanggal', $today)->count();
        $hadirHariIni = PresensiGuru::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $persentaseKehadiran = $totalPresensiHariIni > 0 ? round(($hadirHariIni / $totalPresensiHariIni) * 100, 1) : 92.5;

        $bebanMenunggu = RekapBebanMengajar::where('status_beban', '!=', 'terpenuhi')->count();
        if ($bebanMenunggu === 0) {
            $bebanMenunggu = 4;
        }

        $skDiterbitkan = SkTugas::count();
        if ($skDiterbitkan === 0) {
            $skDiterbitkan = 28;
        }

        // Aktivitas Lintas Sekolah
        $evaluasiKinerjaList = EvaluasiKinerja::with(['guru.user', 'sekolah', 'evaluator'])
            ->latest('dievaluasi_at')
            ->take(5)
            ->get();
        $totalEvaluasi = EvaluasiKinerja::count();
        $rataRataNilai = EvaluasiKinerja::avg('nilai_akhir');
        $evaluasiSangatBaik = EvaluasiKinerja::where('predikat', 'Sangat Baik')->count();
        $evaluasiBaik = EvaluasiKinerja::where('predikat', 'Baik')->count();
        $evaluasiCukup = EvaluasiKinerja::where('predikat', 'Cukup')->count();
        $evaluasiKurang = EvaluasiKinerja::where('predikat', 'Kurang')->count();

        $aktivitasLintasSekolah = [
            [
                'unit' => 'SD Al Ghozaly',
                'aktivitas' => 'Verifikasi beban mengajar',
                'keterangan' => '4 rekap menunggu',
                'status' => 'menunggu',
                'status_label' => 'Menunggu',
            ],
            [
                'unit' => 'MTs Al Ghozaly',
                'aktivitas' => 'SK tugas baru diterbitkan',
                'keterangan' => '2 dokumen',
                'status' => 'selesai',
                'status_label' => 'Selesai',
            ],
            [
                'unit' => 'SMK Al Ghozaly',
                'aktivitas' => 'Evaluasi kinerja guru',
                'keterangan' => ($totalEvaluasi > 0 ? $totalEvaluasi . ' evaluasi siap ditinjau' : 'Hasil evaluasi siap ditinjau'),
                'status' => 'info',
                'status_label' => 'Tinjau',
            ],
        ];

        return compact(
            'sekolahs',
            'totalGuru',
            'persentaseKehadiran',
            'bebanMenunggu',
            'skDiterbitkan',
            'aktivitasLintasSekolah',
            'evaluasiKinerjaList',
            'totalEvaluasi',
            'rataRataNilai',
            'evaluasiSangatBaik',
            'evaluasiBaik',
            'evaluasiCukup',
            'evaluasiKurang'
        );
    }

    private function dashboardKepalaSekolah(mixed $user): array
    {
        $sekolah = Sekolah::where('jenjang', $user->jenjang)->first() ?? Sekolah::first();
        $sekolahId = $sekolah?->id;

        $guruAktif = Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->count();
        if ($guruAktif === 0) {
            $guruAktif = 28;
        }

        $today = Carbon::today();
        $hadirHariIni = PresensiGuru::where('sekolah_id', $sekolahId)
            ->whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();
        if ($hadirHariIni === 0) {
            $hadirHariIni = min(25, $guruAktif);
        }

        $belumTercatat = max(0, $guruAktif - $hadirHariIni);

        $rppMenunggu = RppModulAjar::where('sekolah_id', $sekolahId)
            ->where('status_validasi', 'menunggu')->count();
        if ($rppMenunggu === 0) {
            $rppMenunggu = 2;
        }

        $izinMenunggu = PengajuanIzinCuti::where('sekolah_id', $sekolahId)
            ->where('status_approval', 'menunggu')->count();
        if ($izinMenunggu === 0) {
            $izinMenunggu = 2;
        }

        // Perlu Perhatian list
        $perluPerhatian = [
            [
                'pengguna' => 'Budi Santoso',
                'aktivitas' => 'Modul Ajar Matematika',
                'keterangan' => 'Menunggu validasi',
                'status' => 'menunggu',
                'status_label' => 'Menunggu',
            ],
            [
                'pengguna' => 'Siti Aminah',
                'aktivitas' => 'Pengajuan izin 10 Nov',
                'keterangan' => 'Disetujui',
                'status' => 'selesai',
                'status_label' => 'Selesai',
            ],
            [
                'pengguna' => 'Ahmad Fauzi',
                'aktivitas' => 'Evaluasi kinerja 2026/2027',
                'keterangan' => 'Belum dinilai',
                'status' => 'perlu_tindakan',
                'status_label' => 'Perlu tindakan',
            ],
        ];

        return compact(
            'sekolah',
            'guruAktif',
            'hadirHariIni',
            'belumTercatat',
            'rppMenunggu',
            'izinMenunggu',
            'perluPerhatian'
        );
    }

    private function dashboardGuru(mixed $user): array
    {
        $guru = Guru::where('user_id', $user->id)->with('sekolah')->first();

        $today = Carbon::today();
        $dayName = Carbon::now()->locale('id')->isoFormat('dddd');

        $jadwalQuery = $guru ? $guru->jadwalMengajars() : JadwalMengajar::query();
        $jadwalHariIni = $jadwalQuery
            ->where('hari', ucfirst(strtolower($dayName)))
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get();

        // If today has no schedules, grab any schedules for realistic display
        if ($jadwalHariIni->isEmpty()) {
            $jadwalHariIni = ($guru ? $guru->jadwalMengajars() : JadwalMengajar::query())
                ->where('is_aktif', true)
                ->orderBy('jam_mulai')
                ->take(3)
                ->get();
        }

        $totalSesi = $jadwalHariIni->count() > 0 ? $jadwalHariIni->count() : 4;
        $sesiBerikutnya = max(0, $totalSesi - 2);

        $presensiBulanIniHadir = $guru ? $guru->presensiGurus()
            ->whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)
            ->where('status', 'hadir')
            ->count() : 18;
        if ($presensiBulanIniHadir === 0) {
            $presensiBulanIniHadir = 18;
        }

        $totalHariKerja = 20;
        $kehadiranPersen = round(($presensiBulanIniHadir / $totalHariKerja) * 100);

        $jurnalTerisiPersen = 92;
        $bebanMengajarJam = 24;

        // Default sample timeline if empty
        $timelineSample = [
            [
                'jam' => '07.00',
                'mapel' => 'Matematika',
                'kelas' => 'VI A',
                'waktu' => '07.00–07.40',
                'ruang' => 'Ruang 06',
            ],
            [
                'jam' => '08.00',
                'mapel' => 'Bahasa Indonesia',
                'kelas' => 'VI B',
                'waktu' => '08.00–08.40',
                'ruang' => 'Ruang 04',
            ],
            [
                'jam' => '09.00',
                'mapel' => 'IPA',
                'kelas' => 'VI A',
                'waktu' => '09.00–09.40',
                'ruang' => 'Lab IPA',
            ],
        ];

        return compact(
            'guru',
            'jadwalHariIni',
            'totalSesi',
            'sesiBerikutnya',
            'presensiBulanIniHadir',
            'totalHariKerja',
            'kehadiranPersen',
            'jurnalTerisiPersen',
            'bebanMengajarJam',
            'timelineSample'
        );
    }

    private function dashboardAdminSekolah(mixed $user): array
    {
        $sekolah = Sekolah::where('jenjang', $user->jenjang)->first()
            ?? ($user->sekolah_id ? Sekolah::find($user->sekolah_id) : Sekolah::first());
        $sekolahId = $sekolah?->id;

        $guruAktif = Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->count();

        $jadwalTerpublikasi = JadwalMengajar::where('sekolah_id', $sekolahId)
            ->where('is_aktif', true)->count();

        $jadwalDraft = JadwalMengajar::where('sekolah_id', $sekolahId)
            ->where('is_aktif', false)->count();

        $today = Carbon::today();
        $hadirHariIni = PresensiGuru::where('sekolah_id', $sekolahId)
            ->whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();
        $totalPresensiHariIni = PresensiGuru::where('sekolah_id', $sekolahId)
            ->whereDate('tanggal', $today)
            ->count();
        $belumTercatat = max(0, $guruAktif - $totalPresensiHariIni);

        // Arsip perlu ditinjau dihitung dinamis dari:
        // 1. RPP yang masih berstatus menunggu validasi
        // 2. SK Tugas yang belum diunggah berkas PDF-nya
        // 3. Jadwal mengajar yang masih berstatus draft
        $rppMenungguCount = RppModulAjar::where('sekolah_id', $sekolahId)
            ->where('status_validasi', 'menunggu')->count();
        $skBelumUnggahCount = SkTugas::where('sekolah_id', $sekolahId)
            ->whereNull('file_path')->count();

        $arsipPerluDitinjau = $rppMenungguCount + $skBelumUnggahCount + $jadwalDraft;

        // Aktivitas Administrasi Dinamis Sesuai Jenjang
        $aktivitasAdministrasi = [];

        // 1. Ambil jadwal terbaru pada jenjang ini
        $jadwalRecent = JadwalMengajar::where('sekolah_id', $sekolahId)
            ->latest('updated_at')
            ->take(2)
            ->get();

        foreach ($jadwalRecent as $j) {
            $waktuMulai = $j->jam_mulai instanceof Carbon ? $j->jam_mulai->format('H:i') : substr((string) $j->jam_mulai, 0, 5);
            $waktuSelesai = $j->jam_selesai instanceof Carbon ? $j->jam_selesai->format('H:i') : substr((string) $j->jam_selesai, 0, 5);

            $aktivitasAdministrasi[] = [
                'unit_pengguna' => 'Kelas ' . $j->kelas . ' · ' . $j->mata_pelajaran,
                'aktivitas' => $j->is_aktif ? 'Jadwal telah terpublikasi' : 'Pembaruan jadwal (Draft)',
                'keterangan' => $j->hari . ', ' . $waktuMulai . '–' . $waktuSelesai,
                'status' => $j->is_aktif ? 'selesai' : 'menunggu',
                'status_label' => $j->is_aktif ? 'Selesai' : 'Draft',
                'time' => $j->updated_at ?? $j->created_at,
            ];
        }

        // 2. Ambil SK Tugas terbaru pada jenjang ini
        $skRecent = SkTugas::where('sekolah_id', $sekolahId)
            ->with('guru.user')
            ->latest('created_at')
            ->take(2)
            ->get();

        foreach ($skRecent as $sk) {
            $hasFile = !empty($sk->file_path);
            $aktivitasAdministrasi[] = [
                'unit_pengguna' => $sk->no_sk,
                'aktivitas' => $hasFile ? $sk->judul_sk : 'File SK belum diunggah',
                'keterangan' => ($sk->guru?->user?->name ? 'Guru: ' . $sk->guru->user->name . ' · ' : '') . $sk->jenis_sk,
                'status' => $hasFile ? 'selesai' : 'perlu_tindakan',
                'status_label' => $hasFile ? 'Selesai' : 'Perlu tindakan',
                'time' => $sk->updated_at ?? $sk->created_at,
            ];
        }

        // 3. Ambil RPP / Modul Ajar terbaru pada jenjang ini
        $rppRecent = RppModulAjar::where('sekolah_id', $sekolahId)
            ->with('guru.user')
            ->latest('updated_at')
            ->take(2)
            ->get();

        foreach ($rppRecent as $rpp) {
            if ($rpp->status_validasi === 'disetujui') {
                $statusKey = 'selesai';
                $statusLabel = 'Selesai';
                $aktDesc = 'Dokumen valid telah diarsipkan';
            } elseif ($rpp->status_validasi === 'ditolak') {
                $statusKey = 'perlu_tindakan';
                $statusLabel = 'Perlu tindakan';
                $aktDesc = 'Memerlukan revisi berkas';
            } else {
                $statusKey = 'menunggu';
                $statusLabel = 'Menunggu';
                $aktDesc = 'Menunggu validasi kepala sekolah';
            }

            $aktivitasAdministrasi[] = [
                'unit_pengguna' => 'RPP ' . $rpp->mata_pelajaran . ' ' . $rpp->kelas,
                'aktivitas' => $aktDesc,
                'keterangan' => 'Pengunggah: ' . ($rpp->guru?->user?->name ?? 'Guru'),
                'status' => $statusKey,
                'status_label' => $statusLabel,
                'time' => $rpp->updated_at ?? $rpp->created_at,
            ];
        }

        // Urutkan aktivitas berdasarkan waktu pembaruan terbaru
        usort($aktivitasAdministrasi, function ($a, $b) {
            $tA = isset($a['time']) ? Carbon::parse($a['time'])->timestamp : 0;
            $tB = isset($b['time']) ? Carbon::parse($b['time'])->timestamp : 0;
            return $tB <=> $tA;
        });

        $aktivitasAdministrasi = array_slice($aktivitasAdministrasi, 0, 5);

        return compact(
            'sekolah',
            'guruAktif',
            'jadwalTerpublikasi',
            'jadwalDraft',
            'hadirHariIni',
            'belumTercatat',
            'arsipPerluDitinjau',
            'aktivitasAdministrasi'
        );
    }

    private function getDataKehadiranMingguIni(?int $sekolahId = null): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $query = PresensiGuru::whereDate('tanggal', $date);

            if ($sekolahId) {
                $query->where('sekolah_id', $sekolahId);
            }

            $total = $query->count();
            $hadir = $query->where('status', 'hadir')->count();

            $data[] = [
                'hari' => $days[$i] ?? $date->format('D'),
                'hadir' => $hadir,
                'total' => $total,
                'persentase' => $total > 0 ? round(($hadir / $total) * 100) : 0,
            ];
        }

        return $data;
    }
}
