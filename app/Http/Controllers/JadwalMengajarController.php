<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = JadwalMengajar::with(['guru.user', 'sekolah']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('status')) {
            if ($request->status === 'terpublikasi') {
                $query->where('is_aktif', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_aktif', false);
            }
        }

        if (! $user->isGuru() && $request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($user->isPengurusYayasan() && $request->filled('sekolah_id')) {
            $query->where('sekolah_id', $request->sekolah_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mata_pelajaran', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('ruangan', 'like', "%{$search}%")
                    ->orWhereHas('guru.user', fn ($g) => $g->where('name', 'like', "%{$search}%"));
            });
        }

        $caseHari = "CASE hari
            WHEN 'Senin' THEN 1
            WHEN 'Selasa' THEN 2
            WHEN 'Rabu' THEN 3
            WHEN 'Kamis' THEN 4
            WHEN 'Jumat' THEN 5
            WHEN 'Sabtu' THEN 6
            ELSE 7 END";

        $jadwals = $query->orderByRaw($caseHari)
            ->orderBy('jam_mulai')->paginate(15)->withQueryString();

        $guruQuery = Guru::with('user')->where('status', 'aktif');
        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $guruQuery->where('sekolah_id', $sekolahId);
        }

        $gurus = ($user->isPengurusYayasan() || $user->isAdminSekolah() || $user->isKepalaSekolah())
            ? $guruQuery->get()
            : collect();

        $sekolahs = Sekolah::all();

        return view('jadwal-mengajar.index', compact('jadwals', 'gurus', 'sekolahs'));
    }

    public function create(): View
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk menambah jadwal.');
        }

        $guruQuery = Guru::with('user')->where('status', 'aktif');
        $defaultSekolahId = null;

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $guruQuery->where(function ($q) use ($user, $sekolahId) {
                if ($user->jenjang) {
                    $q->where('jenjang', $user->jenjang);
                }
                if ($sekolahId) {
                    $q->orWhere('sekolah_id', $sekolahId);
                }
            });

            $sekolahs = Sekolah::where('id', $sekolahId)->get();
            if ($sekolahs->isEmpty() && $user->jenjang) {
                $sekolahs = Sekolah::where('jenjang', $user->jenjang)->get();
            }
            $defaultSekolahId = $sekolahs->first()?->id ?? $sekolahId;
        } else {
            // Pengurus Yayasan
            $sekolahs = Sekolah::all();
        }

        $gurus = $guruQuery->get();

        return view('jadwal-mengajar.create', compact('gurus', 'sekolahs', 'defaultSekolahId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki izin untuk menambah jadwal.');
        }

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($sekolahId) {
                $request->merge(['sekolah_id' => $sekolahId]);
            }
        }

        $validated = $request->validate([
            'guru_id' => [
                'required',
                'exists:gurus,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
                        $guru = Guru::find($value);
                        if ($guru && $user->jenjang && $guru->jenjang !== $user->jenjang) {
                            $fail('Guru yang dipilih harus berasal dari jenjang '.$user->jenjang.'.');
                        }
                    }
                },
            ],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:50'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
        ]);

        JadwalMengajar::create($validated);

        return redirect()->route('jadwal-mengajar.index')->with('success', 'Jadwal mengajar berhasil ditambahkan.');
    }

    public function edit(JadwalMengajar $jadwalMengajar): View
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk mengedit jadwal.');
        }

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($jadwalMengajar->sekolah_id != $sekolahId) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah jadwal sekolah lain.');
            }
        }

        $guruQuery = Guru::with('user')->where('status', 'aktif');
        $defaultSekolahId = $jadwalMengajar->sekolah_id;

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            $guruQuery->where(function ($q) use ($user, $sekolahId) {
                if ($user->jenjang) {
                    $q->where('jenjang', $user->jenjang);
                }
                if ($sekolahId) {
                    $q->orWhere('sekolah_id', $sekolahId);
                }
            });

            $sekolahs = Sekolah::where('id', $sekolahId)->get();
            if ($sekolahs->isEmpty() && $user->jenjang) {
                $sekolahs = Sekolah::where('jenjang', $user->jenjang)->get();
            }
        } else {
            $sekolahs = Sekolah::all();
        }

        $gurus = $guruQuery->get();

        return view('jadwal-mengajar.edit', compact('jadwalMengajar', 'gurus', 'sekolahs', 'defaultSekolahId'));
    }

    public function update(Request $request, JadwalMengajar $jadwalMengajar): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk mengedit jadwal.');
        }

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($jadwalMengajar->sekolah_id != $sekolahId) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah jadwal sekolah lain.');
            }
            if ($sekolahId) {
                $request->merge(['sekolah_id' => $sekolahId]);
            }
        }

        $validated = $request->validate([
            'guru_id' => [
                'required',
                'exists:gurus,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
                        $guru = Guru::find($value);
                        if ($guru && $user->jenjang && $guru->jenjang !== $user->jenjang) {
                            $fail('Guru yang dipilih harus berasal dari jenjang '.$user->jenjang.'.');
                        }
                    }
                },
            ],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:50'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['boolean'],
        ]);

        $jadwalMengajar->update($validated);

        return redirect()->route('jadwal-mengajar.index')->with('success', 'Jadwal mengajar berhasil diperbarui.');
    }

    public function destroy(JadwalMengajar $jadwalMengajar): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk menghapus jadwal.');
        }

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($jadwalMengajar->sekolah_id != $sekolahId) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus jadwal sekolah lain.');
            }
        }

        $jadwalMengajar->delete();

        return redirect()->route('jadwal-mengajar.index')->with('success', 'Jadwal mengajar berhasil dihapus.');
    }

    public function publish(JadwalMengajar $jadwalMengajar): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            abort(403, 'Guru tidak memiliki akses untuk mempublikasikan jadwal.');
        }

        if ($user->isAdminSekolah() || $user->isKepalaSekolah()) {
            $sekolahId = $user->sekolah_id ?? Sekolah::where('jenjang', $user->jenjang)->value('id');
            if ($jadwalMengajar->sekolah_id != $sekolahId) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah status jadwal sekolah lain.');
            }
        }

        $jadwalMengajar->update(['is_aktif' => ! $jadwalMengajar->is_aktif]);
        $statusText = $jadwalMengajar->is_aktif ? 'dipublikasikan' : 'diubah menjadi draft';

        return back()->with('success', "Jadwal berhasil {$statusText}.");
    }
}
