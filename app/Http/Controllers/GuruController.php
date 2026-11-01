<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Guru::with(['user', 'sekolah']);

        if ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $query->where('sekolah_id', Sekolah::where('jenjang', $user->jenjang)->value('id'));
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }

        $gurus = $query->latest()->paginate(10)->withQueryString();
        $sekolahs = Sekolah::all();

        return view('guru.index', compact('gurus', 'sekolahs'));
    }

    public function create(): View
    {
        $sekolahs = Sekolah::all();

        return view('guru.create', compact('sekolahs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['nullable', 'string', 'unique:users,username'],
            'password' => ['required', 'min:8'],
            'nip' => ['nullable', 'string'],
            'nuptk' => ['nullable', 'string'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'spesialisasi' => ['required', 'string'],
            'status_kepegawaian' => ['required', 'in:PNS,GTY,GTT,Honorer'],
            'pendidikan_terakhir' => ['nullable', 'string'],
            'tanggal_bergabung' => ['nullable', 'date'],
            'telepon' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::findOrFail($validated['sekolah_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'jenjang' => $sekolah->jenjang,
            'sekolah_id' => $sekolah->id,
            'nip' => $validated['nip'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        Guru::create([
            'user_id' => $user->id,
            'sekolah_id' => $validated['sekolah_id'],
            'nip' => $validated['nip'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'jenjang' => $sekolah->jenjang,
            'spesialisasi' => $validated['spesialisasi'],
            'status_kepegawaian' => $validated['status_kepegawaian'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? null,
        ]);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load(['user', 'sekolah', 'jadwalMengajars', 'presensiGurus' => fn ($q) => $q->latest()->take(10)]);

        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        $sekolahs = Sekolah::all();

        return view('guru.edit', compact('guru', 'sekolahs'));
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$guru->user_id],
            'nip' => ['nullable', 'string'],
            'nuptk' => ['nullable', 'string'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
            'spesialisasi' => ['required', 'string'],
            'status_kepegawaian' => ['required', 'in:PNS,GTY,GTT,Honorer'],
            'pendidikan_terakhir' => ['nullable', 'string'],
            'tanggal_bergabung' => ['nullable', 'date'],
            'status' => ['required', 'in:aktif,nonaktif,cuti'],
            'telepon' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string'],
        ]);

        $sekolah = Sekolah::findOrFail($validated['sekolah_id']);

        $guru->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'jenjang' => $sekolah->jenjang,
            'sekolah_id' => $sekolah->id,
            'nip' => $validated['nip'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        $guru->update([
            'sekolah_id' => $validated['sekolah_id'],
            'nip' => $validated['nip'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'jenjang' => $sekolah->jenjang,
            'spesialisasi' => $validated['spesialisasi'],
            'status_kepegawaian' => $validated['status_kepegawaian'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        $guru->user->delete();

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
