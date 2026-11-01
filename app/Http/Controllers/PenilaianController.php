<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Penilaian::with(['guru.user', 'sekolah']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_penilaian', $request->jenis);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $penilaians = $query->latest('tanggal_penilaian')->paginate(15)->withQueryString();

        return view('penilaian.index', compact('penilaians'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->with('jadwalMengajars')->first();

        return view('penilaian.create', compact('guru'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'mata_pelajaran' => ['required', 'string'],
            'kelas' => ['required', 'string'],
            'nama_siswa' => ['required', 'string'],
            'nis' => ['nullable', 'string'],
            'jenis_penilaian' => ['required', 'in:Ulangan Harian,UTS,UAS,Tugas,Praktik,Portofolio'],
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'tahun_ajaran' => ['required', 'string'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'tanggal_penilaian' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        Penilaian::create([...$validated, 'guru_id' => $guru->id, 'sekolah_id' => $guru->sekolah_id]);

        return redirect()->route('penilaian.index')->with('success', 'Data penilaian berhasil disimpan.');
    }

    public function destroy(Penilaian $penilaian): RedirectResponse
    {
        $penilaian->delete();

        return redirect()->route('penilaian.index')->with('success', 'Data penilaian berhasil dihapus.');
    }
}
