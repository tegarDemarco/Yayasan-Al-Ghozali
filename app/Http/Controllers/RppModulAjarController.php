<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $rpps = $query->latest()->paginate(10)->withQueryString();

        return view('rpp.index', compact('rpps'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->with('sekolah')->first();

        return view('rpp.create', compact('guru'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
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

        RppModulAjar::create([
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

        return redirect()->route('rpp.index')->with('success', 'RPP/Modul Ajar berhasil diunggah. Menunggu validasi.');
    }

    public function validasi(Request $request, RppModulAjar $rppModulAjar): RedirectResponse
    {
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

        $pesan = $request->status_validasi === 'disetujui'
            ? 'RPP/Modul Ajar berhasil divalidasi.'
            : 'RPP/Modul Ajar ditolak.';

        return redirect()->back()->with('success', $pesan);
    }

    public function destroy(RppModulAjar $rppModulAjar): RedirectResponse
    {
        if ($rppModulAjar->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($rppModulAjar->file_path);
        }

        $rppModulAjar->delete();

        return redirect()->route('rpp.index')->with('success', 'RPP/Modul Ajar berhasil dihapus.');
    }
}
