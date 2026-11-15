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

        $skTugas = $query->latest('tanggal_sk')->paginate(10)->withQueryString();

        return view('sk-tugas.index', compact('skTugas'));
    }

    public function create(): View
    {
        $user = auth()->user();
        abort_if($user->isGuru(), 403, 'Guru tidak memiliki izin untuk menerbitkan SK Tugas.');

        if ($user->isPengurusYayasan()) {
            $gurus = Guru::where('status', 'aktif')
                ->with(['user', 'sekolah'])
                ->get()
                ->sortBy(fn ($g) => [$g->sekolah?->jenjang ?? '', $g->user?->name ?? '']);
        } else {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $gurus = Guru::where('sekolah_id', $sekolahId)
                ->where('status', 'aktif')
                ->with(['user', 'sekolah'])
                ->get()
                ->sortBy(fn ($g) => $g->user?->name ?? '');
        }

        return view('sk-tugas.create', compact('gurus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->isGuru(), 403, 'Guru tidak memiliki izin untuk menerbitkan SK Tugas.');

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

        $guru = Guru::with('sekolah')->findOrFail($validated['guru_id']);

        if (! $user->isPengurusYayasan()) {
            $userSekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($guru->sekolah_id !== $userSekolahId) {
                abort(403, 'Anda hanya dapat menerbitkan SK untuk guru di sekolah Anda.');
            }
        }

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
        $user = auth()->user();

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($skTugas->guru_id !== $guru?->id) {
                abort(403, 'Anda hanya dapat melihat SK Tugas milik Anda.');
            }
        } elseif (! $user->isPengurusYayasan()) {
            $userSekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($skTugas->sekolah_id !== $userSekolahId) {
                abort(403, 'Anda hanya dapat melihat SK Tugas di sekolah Anda.');
            }
        }

        $skTugas->load('guru.user', 'sekolah', 'penerbit');

        return view('sk-tugas.show', compact('skTugas'));
    }

    public function destroy(SkTugas $skTugas): RedirectResponse
    {
        $user = auth()->user();
        abort_if($user->isGuru(), 403, 'Guru tidak memiliki izin untuk menghapus SK Tugas.');

        if (! $user->isPengurusYayasan()) {
            $userSekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($skTugas->sekolah_id !== $userSekolahId) {
                abort(403, 'Anda hanya dapat menghapus SK Tugas di sekolah Anda.');
            }
        }

        if ($skTugas->file_path) {
            Storage::disk('public')->delete($skTugas->file_path);
        }
        $skTugas->delete();

        return redirect()->route('sk-tugas.index')->with('success', 'SK Tugas berhasil dihapus.');
    }
}
