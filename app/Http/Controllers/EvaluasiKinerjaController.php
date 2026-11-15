<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKinerja;
use App\Models\Guru;
use App\Models\Sekolah;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluasiKinerjaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = EvaluasiKinerja::with(['guru.user', 'sekolah', 'evaluator']);

        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        } elseif ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isPengurusYayasan() && $request->filled('jenjang')) {
            $sekolahId = Sekolah::where('jenjang', $request->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $evaluasis = $query->latest()->paginate(10)->withQueryString();

        return view('evaluasi-kinerja.index', compact('evaluasis'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        abort_if($user->isGuru(), 403, 'Guru tidak memiliki izin untuk membuat evaluasi kinerja.');

        if ($user->isPengurusYayasan()) {
            $gurus = Guru::where('status', 'aktif')->with(['user', 'sekolah'])->get();
        } else {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $gurus = Guru::where('sekolah_id', $sekolahId)->where('status', 'aktif')->with(['user', 'sekolah'])->get();
        }

        return view('evaluasi-kinerja.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->isGuru(), 403, 'Guru tidak memiliki izin untuk membuat evaluasi kinerja.');

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

        $guru = Guru::findOrFail($validated['guru_id']);
        $sekolahId = $guru->sekolah_id;

        if (! $user->isPengurusYayasan()) {
            $userSekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($guru->sekolah_id !== $userSekolahId) {
                abort(403, 'Anda hanya dapat mengevaluasi guru di sekolah Anda.');
            }
        }

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

        $evaluasi = EvaluasiKinerja::create([
            ...$validated,
            'sekolah_id' => $sekolahId,
            'nilai_akhir' => $nilaiAkhir,
            'predikat' => $predikat,
            'dievaluasi_oleh' => $user->id,
            'dievaluasi_at' => now(),
        ]);

        if ($guru && $guru->user) {
    $guru->user->notify(new AppNotification([
        'title' => 'Evaluasi Kinerja Diterbitkan',
        'message' => "Evaluasi kinerja semester {$validated['semester']} TA {$validated['tahun_ajaran']} telah diterbitkan dengan predikat {$predikat} (Nilai: {$nilaiAkhir}).",
        'url' => route('evaluasi-kinerja.show', $evaluasi->id),
        'type' => 'evaluasi',
        'icon' => 'bar-chart-2',
        'senderName' => $user->name,
    ]));
}

        return redirect()->route('evaluasi-kinerja.index')->with('success', 'Evaluasi kinerja berhasil disimpan.');
    }

    public function show(EvaluasiKinerja $evaluasiKinerja): View
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($evaluasiKinerja->guru_id !== $guru?->id) {
                abort(403, 'Anda hanya dapat melihat evaluasi kinerja milik Anda.');
            }
        } elseif (! $user->isPengurusYayasan()) {
            $userSekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($evaluasiKinerja->sekolah_id !== $userSekolahId) {
                abort(403, 'Anda hanya dapat melihat evaluasi kinerja di sekolah Anda.');
            }
        }

        $evaluasiKinerja->load('guru.user', 'sekolah', 'evaluator');

        return view('evaluasi-kinerja.show', compact('evaluasiKinerja'));
    }
}