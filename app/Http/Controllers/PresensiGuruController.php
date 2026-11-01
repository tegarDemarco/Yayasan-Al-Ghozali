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
        $query = PresensiGuru::with(['guru.user', 'sekolah']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $query->whereDate('tanggal', $tanggal);

        $presensi = $query->latest()->paginate(15)->withQueryString();
        $ringkasan = [
            'hadir' => $query->where('status', 'hadir')->count(),
            'izin' => $query->where('status', 'izin')->count(),
            'sakit' => $query->where('status', 'sakit')->count(),
            'alpha' => $query->where('status', 'alpha')->count(),
        ];

        return view('presensi.index', compact('presensi', 'tanggal', 'ringkasan'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $gurus = Guru::with('user')->where('status', 'aktif')->get();

        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $gurus = $gurus->where('sekolah_id', $sekolahId);
        }

        return view('presensi.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

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
            $validated = $request->validate([
                'guru_id' => ['required', 'exists:gurus,id'],
                'tanggal' => ['required', 'date'],
                'status' => ['required', 'in:hadir,izin,sakit,alpha,cuti'],
                'waktu_masuk' => ['nullable', 'date_format:H:i'],
                'keterangan' => ['nullable', 'string'],
            ]);

            $guru = Guru::findOrFail($validated['guru_id']);
            PresensiGuru::updateOrCreate(
                ['guru_id' => $validated['guru_id'], 'tanggal' => $validated['tanggal']],
                [...$validated, 'sekolah_id' => $guru->sekolah_id, 'dicatat_oleh' => $user->id]
            );
        }

        return redirect()->route('presensi.index')->with('success', 'Presensi berhasil dicatat.');
    }

    public function destroy(PresensiGuru $presensi): RedirectResponse
    {
        $presensi->delete();

        return redirect()->route('presensi.index')->with('success', 'Data presensi berhasil dihapus.');
    }
}
