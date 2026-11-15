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

        if ($user->isKepalaSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        } elseif ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        }

        if ($request->filled('jenjang') && $user->isPengurusYayasan()) {
            $jenjang = $request->jenjang;
            $query->where(function ($q) use ($jenjang) {
                $q->whereHas('sekolah', fn ($s) => $s->where('jenjang', $jenjang))
                  ->orWhereHas('guru', fn ($g) => $g->where('jenjang', $jenjang));
            });
        }

        if ($request->filled('predikat')) {
            $query->where('predikat', $request->predikat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('guru.user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
        }

        $evaluasis = $query->latest()->paginate(10)->withQueryString();

        return view('evaluasi-kinerja.index', compact('evaluasis'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();

        if (! $user->isKepalaSekolah()) {
            abort(403, 'Hanya Kepala Sekolah yang memiliki wewenang untuk menilai kinerja guru.');
        }

        $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
        $gurus = Guru::where('status', 'aktif')
            ->where('sekolah_id', $sekolahId)
            ->with(['user', 'sekolah'])
            ->get();

        return view('evaluasi-kinerja.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isKepalaSekolah()) {
            abort(403, 'Hanya Kepala Sekolah yang memiliki wewenang untuk menilai kinerja guru.');
        }

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
        $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');

        if ($guru->sekolah_id !== $sekolahId) {
            abort(403, 'Anda hanya dapat mengevaluasi guru pada unit sekolah Anda.');
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

        // Sesuai Sequence Diagram 5: Kirim notifikasi ke Pengurus Yayasan
        $pengurusList = \App\Models\User::where('role', 'pengurus_yayasan')->get();
        foreach ($pengurusList as $pengurus) {
            $pengurus->notify(new AppNotification([
                'title' => 'Evaluasi Kinerja Guru Selesai',
                'message' => "Kepala Sekolah ({$user->name}) telah menilai kinerja guru {$guru->user?->name} ({$guru->jenjang}).",
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
                abort(403, 'Anda hanya dapat melihat evaluasi kinerja Anda sendiri.');
            }
        } elseif ($user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($evaluasiKinerja->sekolah_id !== $sekolahId) {
                abort(403, 'Anda tidak memiliki hak untuk melihat evaluasi kinerja sekolah lain.');
            }
        }

        $evaluasiKinerja->load('guru.user', 'sekolah', 'evaluator');

        return view('evaluasi-kinerja.show', compact('evaluasiKinerja'));
    }
}
