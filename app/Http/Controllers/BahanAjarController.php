<?php

namespace App\Http\Controllers;

use App\Models\BahanAjar;
use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BahanAjarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = BahanAjar::with(['guru.user', 'sekolah']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        $bahanAjars = $query->latest()->paginate(10)->withQueryString();

        return view('bahan-ajar.index', compact('bahanAjars'));
    }

    public function create(): View
    {
        return view('bahan-ajar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'mata_pelajaran' => ['required', 'string'],
            'kelas' => ['required', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,png,zip', 'max:20480'],
        ]);

        $file = $request->file('file');
        $filePath = $file->store('bahan-ajar', 'public');

        BahanAjar::create([
            'guru_id' => $guru->id,
            'sekolah_id' => $guru->sekolah_id,
            'judul' => $validated['judul'],
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'kelas' => $validated['kelas'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_path' => $filePath,
            'tipe_file' => $file->getClientOriginalExtension(),
            'ukuran_file' => $file->getSize(),
        ]);

        return redirect()->route('bahan-ajar.index')->with('success', 'Bahan ajar berhasil diunggah.');
    }

    public function destroy(BahanAjar $bahanAjar): RedirectResponse
    {
        Storage::disk('public')->delete($bahanAjar->file_path);
        $bahanAjar->delete();

        return redirect()->route('bahan-ajar.index')->with('success', 'Bahan ajar berhasil dihapus.');
    }
}
