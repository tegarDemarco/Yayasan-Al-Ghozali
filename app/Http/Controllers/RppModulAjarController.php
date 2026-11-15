<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RppModulAjarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = RppModulAjar::with(['guru.user', 'sekolah', 'validator']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        if ($user->isPengurusYayasan() && $request->filled('jenjang')) {
            $query->whereHas('sekolah', fn ($s) => $s->where('jenjang', $request->jenjang));
        }

        if ($request->filled('status')) {
            $statusMap = [
                'valid' => 'disetujui',
                'revisi' => 'ditolak',
                'disetujui' => 'disetujui',
                'ditolak' => 'ditolak',
                'menunggu' => 'menunggu',
            ];
            $statusVal = $statusMap[$request->status] ?? $request->status;
            $query->where('status_validasi', $statusVal);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if (! $user->isGuru() && $request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('mata_pelajaran', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhereHas('guru.user', fn ($g) => $g->where('name', 'like', "%{$search}%"));
            });
        }

        $rpps = $query->latest()->paginate(10)->withQueryString();

        $guruQuery = Guru::with('user')->where('status', 'aktif');
        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $guruQuery->where('sekolah_id', $sekolahId);
        }

        $gurus = ! $user->isGuru() ? $guruQuery->get() : collect();

        return view('rpp.index', compact('rpps', 'gurus'));
    }

    public function show(RppModulAjar $rpp): View
    {
        $rpp->load(['guru.user', 'sekolah', 'validator']);

        return view('rpp.show', compact('rpp'));
    }

    public function create(): View
    {
        $user = auth()->user();
        if (! $user->isGuru()) {
            abort(403, 'Hanya Guru yang dapat menyusun dan mengunggah RPP / Modul Ajar.');
        }

        $guru = Guru::where('user_id', $user->id)->with('sekolah')->first();

        return view('rpp.create', compact('guru'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isGuru()) {
            abort(403, 'Hanya Guru yang dapat menyusun dan mengunggah RPP / Modul Ajar.');
        }

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'mata_pelajaran' => ['required', 'string'],
            'kelas' => ['required', 'string'],
            'tahun_ajaran' => ['required', 'string'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'pertemuan_ke' => ['nullable', 'integer'],
            'tipe' => ['required', 'in:RPP,Modul Ajar'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $filePath = $request->file('file')->store('rpp-modul', 'public');

        $rpp = RppModulAjar::create([
            'guru_id' => $guru->id,
            'sekolah_id' => $guru->sekolah_id,
            'judul' => $validated['judul'],
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'kelas' => $validated['kelas'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'semester' => $validated['semester'],
            'pertemuan_ke' => $validated['pertemuan_ke'] ?? null,
            'tipe' => $validated['tipe'],
            'file_path' => $filePath,
        ]);

        $kepsekAndAdmins = User::where('sekolah_id', $guru->sekolah_id)
            ->whereIn('role', ['kepala_sekolah', 'admin_sekolah'])
            ->get();

        foreach ($kepsekAndAdmins as $receiver) {
            $receiver->notify(new AppNotification(
                title: 'RPP / Modul Ajar Baru Diunggah',
                message: "{$user->name} mengunggah {$validated['tipe']} \"{$validated['judul']}\" untuk mapel {$validated['mata_pelajaran']}.",
                url: route('rpp.index'),
                type: 'rpp',
                icon: 'file-text',
                senderName: $user->name,
            ));
        }

        return redirect()->route('rpp.index')->with('success', 'RPP/Modul Ajar berhasil diunggah. Menunggu validasi.');
    }

    public function validasi(Request $request, RppModulAjar $rppModulAjar): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isKepalaSekolah()) {
            abort(403, 'Hanya Kepala Sekolah yang memiliki wewenang untuk memvalidasi RPP/Modul Ajar.');
        }

        $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
        if ($rppModulAjar->sekolah_id !== $sekolahId) {
            abort(403, 'Tidak memiliki akses untuk memvalidasi RPP sekolah lain.');
        }

        $request->validate([
            'status_validasi' => ['required', 'in:disetujui,ditolak'],
            'catatan_validasi' => ['nullable', 'string'],
        ]);

        $rppModulAjar->update([
            'status_validasi' => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'divalidasi_oleh' => auth()->id(),
            'divalidasi_at' => now(),
        ]);

        $guruUser = $rppModulAjar->guru?->user;
        if ($guruUser) {
            $statusText = $request->status_validasi === 'disetujui' ? 'disetujui' : 'ditolak';
            $guruUser->notify(new AppNotification(
                title: 'Validasi RPP/Modul Ajar: '.ucfirst($statusText),
                message: "Dokumen {$rppModulAjar->tipe} \"{$rppModulAjar->judul}\" telah {$statusText} oleh Kepala Sekolah.",
                url: route('rpp.index'),
                type: $request->status_validasi === 'disetujui' ? 'success' : 'warning',
                icon: $request->status_validasi === 'disetujui' ? 'check-circle' : 'x-circle',
                senderName: auth()->user()->name,
            ));
        }

        $pesan = $request->status_validasi === 'disetujui'
            ? 'RPP/Modul Ajar berhasil divalidasi.'
            : 'RPP/Modul Ajar ditolak.';

        return redirect()->back()->with('success', $pesan);
    }

    public function destroy(RppModulAjar $rpp): RedirectResponse
    {
        $user = auth()->user();

        // Guru hanya bisa hapus RPP miliknya sendiri
        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            if (! $guru || $rpp->guru_id !== $guru->id) {
                abort(403, 'Anda hanya dapat menghapus RPP/Modul Ajar milik Anda sendiri.');
            }
        }

        // Kepala Sekolah & Admin Sekolah hanya bisa hapus RPP dari sekolahnya
        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($rpp->sekolah_id !== $sekolahId) {
                abort(403, 'Anda tidak dapat menghapus RPP/Modul Ajar sekolah lain.');
            }
        }

        if ($rpp->file_path) {
            Storage::disk('public')->delete($rpp->file_path);
        }

        $rpp->delete();

        return redirect()->route('rpp.index')->with('success', 'RPP/Modul Ajar berhasil dihapus.');
    }
}
