<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengajuanIzinCuti;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanIzinCutiController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = PengajuanIzinCuti::with(['guru.user', 'sekolah', 'approver']);

        if ($user->isGuru()) {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id);
        } elseif ($user->isKepalaSekolah() || $user->isAdminSekolah()) {
            $sekolahId = Sekolah::where('jenjang', $user->jenjang)->value('id');
            $query->where('sekolah_id', $sekolahId);
        }

        if ($request->filled('status')) {
            $query->where('status_approval', $request->status);
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        return view('pengajuan-izin.index', compact('pengajuans'));
    }

    public function create(): View
    {
        return view('pengajuan-izin.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'jenis_pengajuan' => ['required', 'in:Izin,Cuti,Sakit,Tugas Luar,Dinas'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'gte:tanggal_mulai'],
            'alasan' => ['required', 'string', 'min:10'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran-izin', 'public');
        }

        $mulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $selesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        PengajuanIzinCuti::create([
            'guru_id' => $guru->id,
            'sekolah_id' => $guru->sekolah_id,
            'jenis_pengajuan' => $validated['jenis_pengajuan'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $jumlahHari,
            'alasan' => $validated['alasan'],
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('pengajuan-izin.index')->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan.');
    }

    public function approve(Request $request, PengajuanIzinCuti $pengajuanIzinCuti): RedirectResponse
    {
        $request->validate([
            'status_approval' => ['required', 'in:disetujui,ditolak'],
            'catatan_approval' => ['nullable', 'string'],
        ]);

        $pengajuanIzinCuti->update([
            'status_approval' => $request->status_approval,
            'catatan_approval' => $request->catatan_approval,
            'disetujui_oleh' => auth()->id(),
            'disetujui_at' => now(),
        ]);

        $pesan = $request->status_approval === 'disetujui'
            ? 'Pengajuan berhasil disetujui.'
            : 'Pengajuan ditolak.';

        return redirect()->back()->with('success', $pesan);
    }

    public function destroy(PengajuanIzinCuti $pengajuanIzinCuti): RedirectResponse
    {
        $pengajuanIzinCuti->delete();

        return redirect()->route('pengajuan-izin.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
