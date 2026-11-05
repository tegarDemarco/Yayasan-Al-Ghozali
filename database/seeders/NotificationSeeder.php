<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kepala Sekolah SD
        $kepsekSd = User::where('username', 'kepsek_sd')->first();
        if ($kepsekSd) {
            $kepsekSd->notify(new AppNotification(
                title: 'Pengajuan Izin/Cuti Baru',
                message: 'Guru mengajukan Cuti Tahunan untuk tanggal 10 Nov s.d 12 Nov 2026.',
                url: route('pengajuan-izin.index'),
                type: 'izin',
                icon: 'calendar',
                senderName: 'Guru SD',
            ));

            $kepsekSd->notify(new AppNotification(
                title: 'RPP/Modul Ajar Perlu Validasi',
                message: 'Dokumen RPP Matematika Kelas 4 telah diunggah dan menunggu validasi.',
                url: route('rpp.index'),
                type: 'rpp',
                icon: 'file-text',
                senderName: 'Guru SD',
            ));
        }

        // 2. Guru
        $gurus = User::where('role', 'guru')->take(3)->get();
        foreach ($gurus as $guruUser) {
            $guruUser->notify(new AppNotification(
                title: 'SK Tugas Baru Diterbitkan',
                message: 'Surat Keputusan Penugasan Semester Genap telah diterbitkan oleh Kepala Sekolah.',
                url: route('sk-tugas.index'),
                type: 'sk_tugas',
                icon: 'award',
                senderName: 'Kepala Sekolah',
            ));

            $guruUser->notify(new AppNotification(
                title: 'RPP Telah Divalidasi',
                message: 'RPP Modul Pembelajaran Anda telah disetujui oleh Kepala Sekolah.',
                url: route('rpp.index'),
                type: 'success',
                icon: 'check-circle',
                senderName: 'Kepala Sekolah',
            ));
        }

        // 3. Pengurus Yayasan
        $pengurus = User::where('role', 'pengurus_yayasan')->first();
        if ($pengurus) {
            $pengurus->notify(new AppNotification(
                title: 'Rekapitulasi Kinerja Siap Ditinjau',
                message: 'Rekap beban mengajar dan evaluasi kinerja seluruh jenjang (SD, MTs, SMK) telah diperbarui.',
                url: route('rekap-beban.index'),
                type: 'evaluasi',
                icon: 'bar-chart-2',
                senderName: 'Sistem Yayasan',
            ));
        }
    }
}
