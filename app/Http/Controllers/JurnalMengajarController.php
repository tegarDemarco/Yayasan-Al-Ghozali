<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JurnalMengajar;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = JurnalMengajar::with(['guru.user', 'jadwal']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $guruIds = Guru::where('sekolah_id', $sekolahId)->pluck('id');
            $query->whereIn('guru_id', $guruIds);
        }

        if ($user->isPengurusYayasan() && $request->filled('jenjang')) {
            $query->whereHas('guru', fn ($g) => $g->where('jenjang', $request->jenjang));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mata_pelajaran', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhere('materi', 'like', "%{$search}%")
                  ->orWhereHas('guru.user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $jurnals = $query->latest('tanggal')->paginate(10)->withQueryString();

        return view('jurnal.index', compact('jurnals'));
    }

    public function create(): View
    {
        $user = auth()->user();
        if (! $user->isGuru()) {
            abort(403, 'Hanya Guru yang dapat mengisi jurnal mengajar.');
        }

        $guru = Guru::where('user_id', $user->id)->with('jadwalMengajars')->first();
        $jadwals = $guru?->jadwalMengajars()->where('is_aktif', true)->get() ?? collect();

        return view('jurnal.create', compact('guru', 'jadwals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isGuru()) {
            abort(403, 'Hanya Guru yang dapat mengisi jurnal mengajar.');
        }

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'jadwal_id' => ['nullable', 'exists:jadwal_mengajars,id'],
            'tanggal' => ['required', 'date'],
            'mata_pelajaran' => ['required', 'string'],
            'kelas' => ['required', 'string'],
            'pertemuan_ke' => ['nullable', 'string'],
            'materi' => ['required', 'string', 'min:10'],
            'kegiatan' => ['nullable', 'string'],
            'jumlah_siswa_hadir' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        JurnalMengajar::create([...$validated, 'guru_id' => $guru->id, 'status' => 'terkirim']);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal mengajar berhasil disimpan.');
    }

    public function show(JurnalMengajar $jurnal): View
    {
        $jurnal->load('guru.user', 'jadwal');

        return view('jurnal.show', compact('jurnal'));
    }

    public function destroy(JurnalMengajar $jurnal): RedirectResponse
    {
        $jurnal->delete();

        return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}
