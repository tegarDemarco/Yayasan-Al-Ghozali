<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\RekapBebanMengajar;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapBebanMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = RekapBebanMengajar::with(['guru.user', 'sekolah']);

        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        } elseif ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('jenjang') && $user->isPengurusYayasan()) {
            $sekolahId = Sekolah::where('jenjang', $request->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $rekaps = $query->latest()->paginate(15)->withQueryString();
        $sekolahs = Sekolah::all();

        return view('rekap-beban.index', compact('rekaps', 'sekolahs'));
    }

    public function generate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'tahun_ajaran' => ['required', 'string'],
            'semester' => ['required', 'in:Ganjil,Genap'],
        ]);

        $gurus = Guru::where('sekolah_id', $validated['sekolah_id'])->where('status', 'aktif')->get();

        foreach ($gurus as $guru) {
            $jadwals = $guru->jadwalMengajars()
                ->where('tahun_ajaran', $validated['tahun_ajaran'])
                ->where('semester', $validated['semester'])
                ->where('is_aktif', true)
                ->get();

            $totalJam = $jadwals->count() * 2;
            $standar = 24;
            $statusBeban = match (true) {
                $totalJam < $standar => 'kurang',
                $totalJam > $standar + 4 => 'lebih',
                default => 'sesuai',
            };

            RekapBebanMengajar::updateOrCreate(
                ['guru_id' => $guru->id, 'tahun_ajaran' => $validated['tahun_ajaran'], 'semester' => $validated['semester']],
                [
                    'sekolah_id' => $validated['sekolah_id'],
                    'total_jam_mengajar' => $totalJam,
                    'jumlah_kelas' => $jadwals->unique('kelas')->count(),
                    'jumlah_mapel' => $jadwals->unique('mata_pelajaran')->count(),
                    'status_beban' => $statusBeban,
                    'standar_jam_minimal' => $standar,
                ]
            );
        }

        return redirect()->route('rekap-beban.index')->with('success', 'Rekap beban mengajar berhasil digenerate.');
    }
}
