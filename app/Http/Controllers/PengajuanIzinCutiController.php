<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengajuanIzinCuti;
use App\Models\Sekolah;
use App\Models\User;
use App\Notifications\AppNotification;
use Carbon\Carbon;
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

        $mulai = Carbon::parse($validated['tanggal_mulai']);
        $selesai = Carbon::parse($validated['tanggal_selesai']);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $guru->id,
            'sekolah_id' => $guru->sekolah_id,
            'jenis_pengajuan' => $validated['jenis_pengajuan'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $jumlahHari,
            'alasan' => $validated['alasan'],
            'lampiran' => $lampiranPath,
        ]);

        $approvers = User::where('sekolah_id', $guru->sekolah_id)
            ->whereIn('role', ['kepala_sekolah', 'admin_sekolah'])
            ->get();

        foreach ($approvers as $approver) {
            $approver->notify(new AppNotification(
                title: 'Pengajuan Izin/Cuti Baru',
                message: "{$user->name} mengajukan {$validated['jenis_pengajuan']} ({$validated['tanggal_mulai']} s.d {$validated['tanggal_selesai']}).",
                url: route('pengajuan-izin.index'),
                type: 'izin',
                icon: 'calendar',
                senderName: $user->name,
            ));
        }

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

        $guruUser = $pengajuanIzinCuti->guru?->user;
        if ($guruUser) {
            $statusText = $request->status_approval === 'disetujui' ? 'disetujui' : 'ditolak';
            $guruUser->notify(new AppNotification(
                title: 'Pengajuan Izin/Cuti '.ucfirst($statusText),
                message: "Pengajuan {$pengajuanIzinCuti->jenis_pengajuan} Anda telah {$statusText}".($request->catatan_approval ? ": \"{$request->catatan_approval}\"" : '.'),
                url: route('pengajuan-izin.index'),
                type: $request->status_approval === 'disetujui' ? 'success' : 'warning',
                icon: $request->status_approval === 'disetujui' ? 'check-circle' : 'x-circle',
                senderName: auth()->user()->name,
            ));
        }

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
