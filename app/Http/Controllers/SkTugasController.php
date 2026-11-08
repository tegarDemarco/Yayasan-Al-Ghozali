<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\SkTugas;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        if ($request->filled('jenjang') && $user->isPengurusYayasan()) {
            $jenjang = $request->jenjang;
            $query->where(function ($q) use ($jenjang) {
                $q->whereHas('sekolah', fn ($s) => $s->where('jenjang', $jenjang))
                  ->orWhereHas('guru', fn ($g) => $g->where('jenjang', $jenjang));
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_sk', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sk', 'like', "%{$search}%")
                  ->orWhere('judul_sk', 'like', "%{$search}%")
                  ->orWhereHas('guru.user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $skTugas = $query->latest('tanggal_sk')->paginate(10)->withQueryString();

        return view('sk-tugas.index', compact('skTugas'));
    }

    public function create(): View
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk menerbitkan SK Tugas.');
        }

        $query = Guru::where('status', 'aktif')->with(['user', 'sekolah']);

        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $gurus = $query->get();

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

        $skTugas = SkTugas::create([
            ...$validated,
            'sekolah_id' => $guru->sekolah_id,
            'file_path' => $filePath,
            'diterbitkan_oleh' => $user->id,
        ]);

        if ($guru->user) {
            $guru->user->notify(new AppNotification(
                title: 'SK Tugas Baru Diterbitkan',
                message: "SK No. {$validated['no_sk']} \"{$validated['judul_sk']}\" telah diterbitkan untuk Anda.",
                url: route('sk-tugas.show', $skTugas->id),
                type: 'sk_tugas',
                icon: 'award',
                senderName: $user->name,
            ));
        }

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
            Storage::disk('public')->delete($skTugas->file_path);
        }
        $skTugas->delete();

        return redirect()->route('sk-tugas.index')->with('success', 'SK Tugas berhasil dihapus.');
    }
}
