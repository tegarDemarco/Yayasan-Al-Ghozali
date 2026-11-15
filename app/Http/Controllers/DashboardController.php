<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKinerja;
use App\Models\Guru;
use App\Models\PengajuanIzinCuti;
use App\Models\PresensiGuru;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use App\Models\SkTugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
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

        $today = Carbon::today();
        $totalPresensiHariIni = PresensiGuru::whereDate('tanggal', $today)->count();
        $hadirHariIni = PresensiGuru::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $persentaseKehadiran = $totalPresensiHariIni > 0 ? round(($hadirHariIni / $totalPresensiHariIni) * 100, 1) : 0;

        $rataKinerja = EvaluasiKinerja::avg('nilai_akhir') ?? 0;

        $aktivitasTerbaru = collect([
            ...PengajuanIzinCuti::with('guru.user')->latest()->take(3)->get()->map(fn ($p) => [
                'tipe' => 'izin',
                'pesan' => "Pengajuan {$p->jenis_pengajuan} dari " . ($p->guru?->user?->name ?? 'Guru'),
                'waktu' => $p->created_at,
                'warna' => 'blue',
            ]),
            ...RppModulAjar::with('guru.user')->latest()->take(3)->get()->map(fn ($r) => [
                'tipe' => 'rpp',
                'pesan' => "{$r->tipe} diunggah oleh " . ($r->guru?->user?->name ?? 'Guru'),
                'waktu' => $r->created_at,
                'warna' => 'green',
            ]),
            ...SkTugas::with('guru.user')->latest()->take(2)->get()->map(fn ($s) => [
                'tipe' => 'sk',
                'pesan' => "SK {$s->jenis_sk} untuk " . ($s->guru?->user?->name ?? 'Guru'),
                'waktu' => $s->created_at,
                'warna' => 'yellow',
            ]),
        ])->sortByDesc('waktu')->take(5)->values();

        $kehadiranPerMinggu = $this->getDataKehadiranMingguIni();

        return compact(
            'sekolahs', 'totalGuru', 'persentaseKehadiran',
            'rataKinerja', 'aktivitasTerbaru', 'kehadiranPerMinggu'
        );
    }

    private function dashboardKepalaSekolah(User $user): array
    {
        $sekolah = $user->sekolah ?? Sekolah::where('jenjang', $user->jenjang)->first();
        $sekolahId = $sekolah?->id;

        $guruAktif = $sekolahId ? Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->count() : 0;

        $today = Carbon::today();
        $presensiHariIni = $sekolahId
            ? PresensiGuru::where('sekolah_id', $sekolahId)->whereDate('tanggal', $today)->get()
            : collect();

        $rppMenunggu = $sekolahId
            ? RppModulAjar::where('sekolah_id', $sekolahId)->where('status_validasi', 'menunggu')->count()
            : 0;

        $izinMenunggu = $sekolahId
            ? PengajuanIzinCuti::where('sekolah_id', $sekolahId)->where('status_approval', 'menunggu')->count()
            : 0;

        $kehadiranPerMinggu = $sekolahId ? $this->getDataKehadiranMingguIni($sekolahId) : [];

        return compact('sekolah', 'guruAktif', 'presensiHariIni', 'rppMenunggu', 'izinMenunggu', 'kehadiranPerMinggu');
    }

    private function dashboardGuru(User $user): array
    {
        $guru = Guru::where('user_id', $user->id)->with('sekolah')->first();
        if (! $guru) {
            return [
                'guru' => null,
                'jadwalHariIni' => collect(),
                'presensiHariIni' => null,
                'jurnalBulanIni' => 0,
                'pengajuanMenunggu' => 0,
            ];
        }

        $today = Carbon::today();
        $dayName = Carbon::now()->locale('id')->isoFormat('dddd');
        $jadwalHariIni = $guru->jadwalMengajars()
            ->where('hari', ucfirst(strtolower($dayName)))
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get();

        $presensiHariIni = $guru->presensiGurus()->whereDate('tanggal', $today)->first();

        $jurnalBulanIni = $guru->jurnalMengajars()
            ->whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)
            ->count();

        $pengajuanMenunggu = $guru->pengajuanIzinCutis()
            ->where('status_approval', 'menunggu')->count();

        return compact(
            'guru', 'jadwalHariIni', 'presensiHariIni',
            'jurnalBulanIni', 'pengajuanMenunggu'
        );
    }

    private function dashboardAdminSekolah(User $user): array
    {
        $sekolah = $user->sekolah ?? Sekolah::where('jenjang', $user->jenjang)->first();
        $sekolahId = $sekolah?->id;

        $guruAktif = $sekolahId ? Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->count() : 0;

        $today = Carbon::today();
        $presensiHariIni = $sekolahId
            ? PresensiGuru::where('sekolah_id', $sekolahId)->whereDate('tanggal', $today)->get()
            : collect();

        $izinMenunggu = $sekolahId
            ? PengajuanIzinCuti::where('sekolah_id', $sekolahId)->where('status_approval', 'menunggu')->count()
            : 0;

        return compact('sekolah', 'guruAktif', 'presensiHariIni', 'izinMenunggu');
    }

    private function getDataKehadiranMingguIni(?int $sekolahId = null): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $data = [];
        $startOfWeek = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $query = PresensiGuru::whereDate('tanggal', $date);

            if ($sekolahId) {
                $query->where('sekolah_id', $sekolahId);
            }

            $total = (clone $query)->count();
            $hadir = (clone $query)->where('status', 'hadir')->count();

            $data[] = [
                'hari' => $days[$i],
                'hadir' => $hadir,
                'total' => $total,
                'persentase' => $total > 0 ? round(($hadir / $total) * 100) : 0,
            ];
        }

        return $data;
    }
}
