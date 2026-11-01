<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\SkTugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkTugasController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = SkTugas::with(['guru.user', 'sekolah', 'penerbit']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $skTugas = $query->latest('tanggal_sk')->paginate(10)->withQueryString();

        return view('sk-tugas.index', compact('skTugas'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
        $gurus = Guru::where('sekolah_id', $sekolahId ?? null)->where('status', 'aktif')->with('user')->get();

        return view('sk-tugas.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'guru_id' => ['required', 'exists:gurus,id'],
            'no_sk' => ['required', 'string', 'unique:sk_tugas,no_sk'],
            'judul_sk' => ['required', 'string'],
            'jenis_sk' => ['required', 'in:Pengangkatan,Pemberhentian,Mutasi,Penghargaan,Sanksi,Tugas Tambahan'],
            'tanggal_sk' => ['required', 'date'],
            'berlaku_mulai' => ['nullable', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'keterangan' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $guru = Guru::findOrFail($validated['guru_id']);
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('sk-tugas', 'public');
        }

        SkTugas::create([
            ...$validated,
            'sekolah_id' => $guru->sekolah_id,
            'file_path' => $filePath,
            'diterbitkan_oleh' => $user->id,
        ]);

        return redirect()->route('sk-tugas.index')->with('success', 'SK Tugas berhasil diterbitkan.');
    }

    public function show(SkTugas $skTugas): View
    {
        $skTugas->load('guru.user', 'sekolah', 'penerbit');

        return view('sk-tugas.show', compact('skTugas'));
    }

    public function destroy(SkTugas $skTugas): RedirectResponse
    {
        if ($skTugas->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($skTugas->file_path);
        }
        $skTugas->delete();

        return redirect()->route('sk-tugas.index')->with('success', 'SK Tugas berhasil dihapus.');
    }
}
