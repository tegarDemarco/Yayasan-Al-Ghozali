<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PresensiGuru;
use App\Models\Sekolah;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiGuruController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'harian'); // 'harian' atau 'rekap'

        $sekolahId = null;
        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
        }

        // -------------------------------------------------------------
        // MODE 1: REKAPITULASI BULANAN
        // -------------------------------------------------------------
        if ($tab === 'rekap') {
            $bulan = (int) $request->get('bulan', Carbon::now()->month);
            $tahun = (int) $request->get('tahun', Carbon::now()->year);

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];

            $gurusQuery = Guru::with(['user', 'sekolah'])->where('status', 'aktif');
            if ($user->isGuru()) {
                $gurusQuery->where('user_id', $user->id);
            } elseif ($sekolahId) {
                $gurusQuery->where('sekolah_id', $sekolahId);
            }

            if ($request->filled('jenjang') && $user->isPengurusYayasan()) {
                $gurusQuery->where('jenjang', $request->jenjang);
            }

            $gurus = $gurusQuery->orderBy('jenjang')->get();

            // Ambil semua transaksi presensi pada bulan & tahun terpilih
            $presensiBulanQuery = PresensiGuru::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);

            if ($user->isGuru()) {
                $guruModel = Guru::where('user_id', $user->id)->first();
                $presensiBulanQuery->where('guru_id', $guruModel?->id);
            } elseif ($sekolahId) {
                $presensiBulanQuery->where('sekolah_id', $sekolahId);
            }

            $presensiGrouped = $presensiBulanQuery->get()->groupBy('guru_id');

            // Akumulasi statistik kehadiran per guru
            $rekapGuru = [];
            $totalHadirBulan = 0;
            $totalIzinBulan = 0;
            $totalSakitBulan = 0;
            $totalAlphaBulan = 0;
            $totalCutiBulan = 0;

            foreach ($gurus as $g) {
                $records = $presensiGrouped->get($g->id, collect());
                $hadir = $records->where('status', 'hadir')->count();
                $izin = $records->where('status', 'izin')->count();
                $sakit = $records->where('status', 'sakit')->count();
                $alpha = $records->where('status', 'alpha')->count();
                $cuti = $records->where('status', 'cuti')->count();
                $totalRecord = $records->count();

                $persenHadir = $totalRecord > 0 ? round(($hadir / $totalRecord) * 100, 1) : 0;

                $totalHadirBulan += $hadir;
                $totalIzinBulan += $izin;
                $totalSakitBulan += $sakit;
                $totalAlphaBulan += $alpha;
                $totalCutiBulan += $cuti;

                $rekapGuru[] = [
                    'guru' => $g,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpha' => $alpha,
                    'cuti' => $cuti,
                    'total' => $totalRecord,
                    'persentase' => $persenHadir,
                ];
            }

            $ringkasanBulan = [
                'total_guru' => $gurus->count(),
                'hadir' => $totalHadirBulan,
                'izin' => $totalIzinBulan,
                'sakit' => $totalSakitBulan,
                'alpha' => $totalAlphaBulan,
                'cuti' => $totalCutiBulan,
            ];

            return view('presensi.index', compact(
                'tab', 'bulan', 'tahun', 'namaBulan', 'rekapGuru', 'ringkasanBulan'
            ));
        }

        // -------------------------------------------------------------
        // MODE 2: PRESENSI HARIAN (DEFAULT)
        // -------------------------------------------------------------
        $query = PresensiGuru::with(['guru.user', 'sekolah']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $query->whereDate('tanggal', $tanggal);

        $presensi = $query->latest()->paginate(15)->withQueryString();
        $ringkasan = [
            'hadir' => (clone $query)->where('status', 'hadir')->count(),
            'izin' => (clone $query)->where('status', 'izin')->count(),
            'sakit' => (clone $query)->where('status', 'sakit')->count(),
            'alpha' => (clone $query)->where('status', 'alpha')->count(),
        ];

        return view('presensi.index', compact('tab', 'presensi', 'tanggal', 'ringkasan'));
    }

    public function create(): View
    {
        $user = auth()->user();

        if ($user->isKepalaSekolah() || $user->isPengurusYayasan()) {
            abort(403, 'Anda hanya memiliki wewenang untuk memantau data presensi.');
        }

        $gurusQuery = Guru::with('user')->where('status', 'aktif');

        if ($user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $gurusQuery->where('sekolah_id', $sekolahId);
        } elseif ($user->isGuru()) {
            $gurusQuery->where('user_id', $user->id);
        }

        $gurus = $gurusQuery->get();

        return view('presensi.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isKepalaSekolah() || $user->isPengurusYayasan()) {
            abort(403, 'Anda hanya memiliki wewenang untuk memantau data presensi.');
        }

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->firstOrFail();
            $validated = $request->validate([
                'tanggal' => ['required', 'date'],
                'status' => ['required', 'in:hadir,izin,sakit,alpha,cuti'],
                'waktu_masuk' => ['nullable', 'date_format:H:i'],
                'waktu_keluar' => ['nullable', 'date_format:H:i'],
                'keterangan' => ['nullable', 'string'],
            ]);

            PresensiGuru::updateOrCreate(
                ['guru_id' => $guru->id, 'tanggal' => $validated['tanggal']],
                [...$validated, 'sekolah_id' => $guru->sekolah_id, 'dicatat_oleh' => $user->id]
            );
        } else {
            // Admin Sekolah
            $validated = $request->validate([
                'guru_id' => ['required', 'exists:gurus,id'],
                'tanggal' => ['required', 'date'],
                'status' => ['required', 'in:hadir,izin,sakit,alpha,cuti'],
                'waktu_masuk' => ['nullable', 'date_format:H:i'],
                'keterangan' => ['nullable', 'string'],
            ]);

            $guru = Guru::findOrFail($validated['guru_id']);
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($guru->sekolah_id !== $sekolahId) {
                abort(403, 'Anda tidak dapat mencatat presensi guru sekolah lain.');
            }

            PresensiGuru::updateOrCreate(
                ['guru_id' => $validated['guru_id'], 'tanggal' => $validated['tanggal']],
                [...$validated, 'sekolah_id' => $guru->sekolah_id, 'dicatat_oleh' => $user->id]
            );
        }

        return redirect()->route('presensi.index')->with('success', 'Presensi berhasil dicatat.');
    }

    public function destroy(PresensiGuru $presensi): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isKepalaSekolah() || $user->isPengurusYayasan()) {
            abort(403, 'Anda tidak diizinkan menghapus data presensi.');
        }

        if ($user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($presensi->sekolah_id !== $sekolahId) {
                abort(403, 'Anda tidak dapat menghapus presensi sekolah lain.');
            }
        }

        $presensi->delete();

        return redirect()->route('presensi.index')->with('success', 'Data presensi berhasil dihapus.');
    }
}
