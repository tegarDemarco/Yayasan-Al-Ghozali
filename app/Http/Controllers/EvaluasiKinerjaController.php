<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKinerja;
use App\Models\Guru;
use App\Models\PresensiGuru;
use App\Models\Sekolah;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluasiKinerjaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = EvaluasiKinerja::with(['guru.user', 'sekolah', 'evaluator']);

        if ($user->isKepalaSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        } elseif ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        }

        $evaluasis = $query->latest()->paginate(10)->withQueryString();

        return view('evaluasi-kinerja.index', compact('evaluasis'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
        $gurus = Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->with('user')->get();

        return view('evaluasi-kinerja.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');

        $validated = $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'tahun_ajaran' => ['required', 'string'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'nilai_kinerja' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_kehadiran' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_administrasi' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_pembelajaran' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $nilaiAkhir = (
            $validated['nilai_kinerja'] * 0.3 +
            $validated['nilai_kehadiran'] * 0.25 +
            $validated['nilai_administrasi'] * 0.2 +
            $validated['nilai_pembelajaran'] * 0.25
        );

        $predikat = match (true) {
            $nilaiAkhir >= 90 => 'Sangat Baik',
            $nilaiAkhir >= 75 => 'Baik',
            $nilaiAkhir >= 60 => 'Cukup',
            default => 'Kurang',
        };

        EvaluasiKinerja::create([
            ...$validated,
            'sekolah_id' => $sekolahId,
            'nilai_akhir' => $nilaiAkhir,
            'predikat' => $predikat,
            'dievaluasi_oleh' => $user->id,
            'dievaluasi_at' => now(),
        ]);

        return redirect()->route('evaluasi-kinerja.index')->with('success', 'Evaluasi kinerja berhasil disimpan.');
    }

    public function show(EvaluasiKinerja $evaluasiKinerja): View
    {
        $evaluasiKinerja->load('guru.user', 'sekolah', 'evaluator');

        return view('evaluasi-kinerja.show', compact('evaluasiKinerja'));
    }
}
