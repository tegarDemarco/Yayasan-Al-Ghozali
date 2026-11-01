<?php

namespace Database\Seeders;

use App\Models\EvaluasiKinerja;
use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Penilaian;
use App\Models\PengajuanIzinCuti;
use App\Models\PresensiGuru;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use App\Models\SkTugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 3 schools
        $sd = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '20101001',
            'akreditasi' => 'A',
            'alamat' => 'Jl. Pendidikan No. 1, Brebes, Jawa Tengah',
            'telepon' => '0283-123456',
            'email' => 'sd@alghozaly.sch.id',
            'kepala_sekolah' => 'Drs. Ahmad Kusuma, M.Pd',
        ]);

        $mts = Sekolah::create([
            'nama_sekolah' => 'MTs Al-Ghozaly',
            'jenjang' => 'MTs',
            'npsn' => '20101002',
            'akreditasi' => 'A',
            'alamat' => 'Jl. Pendidikan No. 2, Brebes, Jawa Tengah',
            'telepon' => '0283-123457',
            'email' => 'mts@alghozaly.sch.id',
            'kepala_sekolah' => 'Dra. Siti Rahayu, M.Pd',
        ]);

        $smk = Sekolah::create([
            'nama_sekolah' => 'SMK Al-Ghozaly',
            'jenjang' => 'SMK',
            'npsn' => '20101003',
            'akreditasi' => 'B',
            'alamat' => 'Jl. Pendidikan No. 3, Brebes, Jawa Tengah',
            'telepon' => '0283-123458',
            'email' => 'smk@alghozaly.sch.id',
            'kepala_sekolah' => 'H. Bambang Sutrisno, S.T., M.T',
        ]);

        // Pengurus Yayasan
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'pengurus@alghozaly.sch.id',
            'username' => 'pengurus',
            'password' => Hash::make('password'),
            'role' => 'pengurus_yayasan',
            'jabatan' => 'Ketua Yayasan',
            'is_active' => true,
        ]);

        // Kepala Sekolah SD
        User::create([
            'name' => 'Drs. Ahmad Kusuma, M.Pd',
            'email' => 'kepsek.sd@alghozaly.sch.id',
            'username' => 'kepsek_sd',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $sd->id,
            'jabatan' => 'Kepala Sekolah SD',
            'nip' => '197001012000031001',
            'is_active' => true,
        ]);

        // Kepala Sekolah MTs
        User::create([
            'name' => 'Dra. Siti Rahayu, M.Pd',
            'email' => 'kepsek.mts@alghozaly.sch.id',
            'username' => 'kepsek_mts',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
            'jenjang' => 'MTs',
            'sekolah_id' => $mts->id,
            'jabatan' => 'Kepala Sekolah MTs',
            'nip' => '197001012000032001',
            'is_active' => true,
        ]);

        // Kepala Sekolah SMK
        User::create([
            'name' => 'H. Bambang Sutrisno, S.T., M.T',
            'email' => 'kepsek.smk@alghozaly.sch.id',
            'username' => 'kepsek_smk',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
            'jenjang' => 'SMK',
            'sekolah_id' => $smk->id,
            'jabatan' => 'Kepala Sekolah SMK',
            'nip' => '197001012000033001',
            'is_active' => true,
        ]);

        // Admin Sekolah SD
        User::create([
            'name' => 'Dewi Lestari',
            'email' => 'admin.sd@alghozaly.sch.id',
            'username' => 'admin_sd',
            'password' => Hash::make('password'),
            'role' => 'admin_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $sd->id,
            'jabatan' => 'Tata Usaha SD',
            'is_active' => true,
        ]);

        // Admin Sekolah MTs
        User::create([
            'name' => 'Rina Marlina',
            'email' => 'admin.mts@alghozaly.sch.id',
            'username' => 'admin_mts',
            'password' => Hash::make('password'),
            'role' => 'admin_sekolah',
            'jenjang' => 'MTs',
            'sekolah_id' => $mts->id,
            'jabatan' => 'Tata Usaha MTs',
            'is_active' => true,
        ]);

        // Admin Sekolah SMK
        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'admin.smk@alghozaly.sch.id',
            'username' => 'admin_smk',
            'password' => Hash::make('password'),
            'role' => 'admin_sekolah',
            'jenjang' => 'SMK',
            'sekolah_id' => $smk->id,
            'jabatan' => 'Tata Usaha SMK',
            'is_active' => true,
        ]);

        // Create Guru for SD
        $guruSdData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@alghozaly.sch.id', 'username' => 'guru_sd1', 'mapel' => 'Matematika'],
            ['name' => 'Siti Aminah', 'email' => 'siti.aminah@alghozaly.sch.id', 'username' => 'guru_sd2', 'mapel' => 'Bahasa Indonesia'],
            ['name' => 'Budi Hartono', 'email' => 'budi.hartono@alghozaly.sch.id', 'username' => 'guru_sd3', 'mapel' => 'IPA'],
            ['name' => 'Rina Martina', 'email' => 'rina.martina@alghozaly.sch.id', 'username' => 'guru_sd4', 'mapel' => 'PJOK'],
        ];

        foreach ($guruSdData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'jenjang' => 'SD',
                'sekolah_id' => $sd->id,
                'is_active' => true,
            ]);

            $guru = Guru::create([
                'user_id' => $user->id,
                'sekolah_id' => $sd->id,
                'jenjang' => 'SD',
                'spesialisasi' => $data['mapel'],
                'status_kepegawaian' => 'GTY',
                'pendidikan_terakhir' => 'S1',
                'tanggal_bergabung' => Carbon::now()->subYears(rand(1, 10)),
                'status' => 'aktif',
            ]);

            // Add jadwal mengajar
            $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            foreach (array_slice($haris, 0, rand(3, 5)) as $idx => $hari) {
                JadwalMengajar::create([
                    'guru_id' => $guru->id,
                    'sekolah_id' => $sd->id,
                    'mata_pelajaran' => $data['mapel'],
                    'kelas' => 'Kelas '.['IV-A', 'V-B', 'VI-A', 'VI-B'][$idx % 4],
                    'hari' => $hari,
                    'jam_mulai' => ['08:00', '09:40', '11:00', '13:00'][$idx % 4],
                    'jam_selesai' => ['09:20', '11:00', '12:20', '14:20'][$idx % 4],
                    'tahun_ajaran' => '2025/2026',
                    'semester' => 'Ganjil',
                    'is_aktif' => true,
                ]);
            }

            // Add presensi for last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                if ($date->isWeekday()) {
                    PresensiGuru::create([
                        'guru_id' => $guru->id,
                        'sekolah_id' => $sd->id,
                        'tanggal' => $date,
                        'status' => rand(0, 10) > 1 ? 'hadir' : 'izin',
                        'waktu_masuk' => '07:15',
                    ]);
                }
            }

            // Add evaluasi kinerja
            EvaluasiKinerja::create([
                'guru_id' => $guru->id,
                'sekolah_id' => $sd->id,
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Ganjil',
                'nilai_kinerja' => rand(75, 95),
                'nilai_kehadiran' => rand(80, 98),
                'nilai_administrasi' => rand(70, 95),
                'nilai_pembelajaran' => rand(75, 95),
                'nilai_akhir' => rand(78, 92),
                'predikat' => 'Baik',
                'dievaluasi_oleh' => 2,
                'dievaluasi_at' => now(),
            ]);
        }

        // Create Guru for MTs
        $guruMtsData = [
            ['name' => 'Ustaz Ahmad Rifai', 'email' => 'ahmad.rifai@alghozaly.sch.id', 'username' => 'guru_mts1', 'mapel' => 'Fiqih'],
            ['name' => 'Nur Hidayah', 'email' => 'nur.hidayah@alghozaly.sch.id', 'username' => 'guru_mts2', 'mapel' => 'Bahasa Arab'],
            ['name' => 'Hendra Saputra', 'email' => 'hendra.s@alghozaly.sch.id', 'username' => 'guru_mts3', 'mapel' => 'Matematika'],
        ];

        foreach ($guruMtsData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'jenjang' => 'MTs',
                'sekolah_id' => $mts->id,
                'is_active' => true,
            ]);

            $guru = Guru::create([
                'user_id' => $user->id,
                'sekolah_id' => $mts->id,
                'jenjang' => 'MTs',
                'spesialisasi' => $data['mapel'],
                'status_kepegawaian' => 'GTY',
                'pendidikan_terakhir' => 'S1',
                'tanggal_bergabung' => Carbon::now()->subYears(rand(1, 8)),
                'status' => 'aktif',
            ]);

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                if ($date->isWeekday()) {
                    PresensiGuru::create([
                        'guru_id' => $guru->id,
                        'sekolah_id' => $mts->id,
                        'tanggal' => $date,
                        'status' => rand(0, 10) > 1 ? 'hadir' : 'sakit',
                        'waktu_masuk' => '07:30',
                    ]);
                }
            }
        }

        // Create Guru for SMK
        $guruSmkData = [
            ['name' => 'Ir. Dian Prabowo', 'email' => 'dian.p@alghozaly.sch.id', 'username' => 'guru_smk1', 'mapel' => 'Teknik Komputer'],
            ['name' => 'Lestari Wahyuni', 'email' => 'lestari.w@alghozaly.sch.id', 'username' => 'guru_smk2', 'mapel' => 'Akuntansi'],
        ];

        foreach ($guruSmkData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'jenjang' => 'SMK',
                'sekolah_id' => $smk->id,
                'is_active' => true,
            ]);

            Guru::create([
                'user_id' => $user->id,
                'sekolah_id' => $smk->id,
                'jenjang' => 'SMK',
                'spesialisasi' => $data['mapel'],
                'status_kepegawaian' => 'GTT',
                'pendidikan_terakhir' => 'S1',
                'tanggal_bergabung' => Carbon::now()->subYears(rand(1, 5)),
                'status' => 'aktif',
            ]);
        }

        // ===== SK TUGAS DATA =====
        $allGurus = Guru::with(['user', 'sekolah'])->get();

        $skTemplates = [
            ['SD', 'Pengangkatan', 'SK Pengangkatan sebagai Guru Tetap Yayasan Al-Ghozaly', 2],
            ['MTs', 'Pengangkatan', 'SK Pengangkatan sebagai Guru Tetap Yayasan Al-Ghozaly', 3],
            ['SMK', 'Pengangkatan', 'SK Pengangkatan sebagai Guru Tetap Yayasan Al-Ghozaly', 4],
            ['SD', 'Tugas Tambahan', 'SK Penugasan sebagai Wali Kelas', 2],
            ['MTs', 'Tugas Tambahan', 'SK Penugasan sebagai Pembina Ekstrakurikuler', 3],
            ['SMK', 'Penghargaan', 'SK Penghargaan Guru Berprestasi Semester Ganjil 2025/2026', 4],
        ];

        $skCounter = ['SD' => 1, 'MTs' => 1, 'SMK' => 1];

        foreach ($allGurus as $guru) {
            $jenjang = $guru->jenjang;
            $kepsekId = match($jenjang) {
                'SD'  => User::where('username', 'kepsek_sd')->value('id'),
                'MTs' => User::where('username', 'kepsek_mts')->value('id'),
                'SMK' => User::where('username', 'kepsek_smk')->value('id'),
            };

            // Pengangkatan SK untuk setiap guru
            $noSk = sprintf('SK/%03d/YAG/%s/%d', $skCounter[$jenjang]++, $jenjang, date('Y'));
            \App\Models\SkTugas::create([
                'guru_id'          => $guru->id,
                'sekolah_id'       => $guru->sekolah_id,
                'no_sk'            => $noSk,
                'judul_sk'         => 'SK Pengangkatan sebagai Guru Tetap Yayasan – ' . $guru->spesialisasi,
                'jenis_sk'         => 'Pengangkatan',
                'tanggal_sk'       => $guru->tanggal_bergabung ?? Carbon::now()->subYear(),
                'berlaku_mulai'    => $guru->tanggal_bergabung ?? Carbon::now()->subYear(),
                'berlaku_sampai'   => null,
                'keterangan'       => 'Ditetapkan berdasarkan hasil seleksi dan penilaian kinerja. Guru dinyatakan memenuhi syarat sebagai tenaga pengajar tetap di ' . $guru->sekolah->nama_sekolah . '.',
                'diterbitkan_oleh' => $kepsekId,
            ]);

            // SK Tugas Tambahan untuk beberapa guru
            if ($guru->id % 2 === 0) {
                $noSk2 = sprintf('SK/%03d/YAG/%s/%d', $skCounter[$jenjang]++, $jenjang, date('Y'));
                \App\Models\SkTugas::create([
                    'guru_id'          => $guru->id,
                    'sekolah_id'       => $guru->sekolah_id,
                    'no_sk'            => $noSk2,
                    'judul_sk'         => 'SK Penugasan sebagai Wali Kelas Tahun Ajaran 2025/2026',
                    'jenis_sk'         => 'Tugas Tambahan',
                    'tanggal_sk'       => Carbon::now()->subMonths(3),
                    'berlaku_mulai'    => Carbon::parse('2025-07-15'),
                    'berlaku_sampai'   => Carbon::parse('2026-06-30'),
                    'keterangan'       => 'Ditugaskan sebagai wali kelas dalam rangka meningkatkan kualitas bimbingan dan pembinaan peserta didik pada tahun ajaran 2025/2026.',
                    'diterbitkan_oleh' => $kepsekId,
                ]);
            }
        }

        // ===== RPP & MODUL AJAR DATA =====
        $sdGurus = Guru::where('jenjang', 'SD')->with('user')->get();
        $mtsGurus = Guru::where('jenjang', 'MTs')->with('user')->get();
        $smkGurus = Guru::where('jenjang', 'SMK')->with('user')->get();
        $kepsekSdId = User::where('username', 'kepsek_sd')->value('id');
        $kepsekMtsId = User::where('username', 'kepsek_mts')->value('id');
        $kepsekSmkId = User::where('username', 'kepsek_smk')->value('id');

        foreach ($sdGurus as $guru) {
            foreach (['RPP', 'Modul Ajar'] as $tipe) {
                \App\Models\RppModulAjar::create([
                    'guru_id'         => $guru->id,
                    'sekolah_id'      => $guru->sekolah_id,
                    'tipe'            => $tipe,
                    'judul'           => $tipe . ' ' . $guru->spesialisasi . ' Kelas IV Semester Ganjil',
                    'mata_pelajaran'  => $guru->spesialisasi,
                    'kelas'           => 'Kelas IV-A',
                    'tahun_ajaran'    => '2025/2026',
                    'semester'        => 'Ganjil',
                    'pertemuan_ke'    => 1,
                    'status_validasi' => $tipe === 'RPP' ? 'disetujui' : 'menunggu',
                    'catatan_validasi'=> $tipe === 'RPP' ? 'Dokumen sudah lengkap dan sesuai standar kurikulum.' : null,
                    'divalidasi_oleh' => $tipe === 'RPP' ? $kepsekSdId : null,
                    'divalidasi_at'   => $tipe === 'RPP' ? Carbon::now()->subDays(5) : null,
                ]);
            }
        }

        foreach ($mtsGurus as $guru) {
            \App\Models\RppModulAjar::create([
                'guru_id'         => $guru->id,
                'sekolah_id'      => $guru->sekolah_id,
                'tipe'            => 'RPP',
                'judul'           => 'RPP ' . $guru->spesialisasi . ' Kelas VII Semester Ganjil',
                'mata_pelajaran'  => $guru->spesialisasi,
                'kelas'           => 'VII-A',
                'tahun_ajaran'    => '2025/2026',
                'semester'        => 'Ganjil',
                'pertemuan_ke'    => 1,
                'status_validasi' => 'disetujui',
                'catatan_validasi'=> 'Telah diverifikasi. Format sudah sesuai dengan standar madrasah.',
                'divalidasi_oleh' => $kepsekMtsId,
                'divalidasi_at'   => Carbon::now()->subDays(3),
            ]);
        }

        // ===== JURNAL MENGAJAR DATA =====
        foreach ($allGurus as $guru) {
            for ($i = 1; $i <= 3; $i++) {
                \App\Models\JurnalMengajar::create([
                    'guru_id'            => $guru->id,
                    'tanggal'            => Carbon::now()->subDays($i * 2),
                    'mata_pelajaran'     => $guru->spesialisasi,
                    'kelas'              => match($guru->jenjang) {
                        'SD' => 'Kelas '.['IV-A','V-B','VI-A'][$i-1],
                        'MTs' => ['VII-A','VIII-B','IX-A'][$i-1],
                        default => ['X TKJ','XI AK','XII TKJ'][$i-1],
                    },
                    'pertemuan_ke'       => $i,
                    'materi'             => 'Materi Pertemuan ' . $i . ': Pokok bahasan ' . $guru->spesialisasi . ' – konsep dasar dan penerapan dalam kehidupan sehari-hari.',
                    'kegiatan'           => 'Pendahuluan (10 mnt): Apersepsi dan motivasi. Inti (60 mnt): Penjelasan materi, diskusi kelompok, latihan soal. Penutup (10 mnt): Kesimpulan dan evaluasi.',
                    'jumlah_siswa_hadir' => rand(28, 35),
                    'catatan'            => $i === 2 ? 'Beberapa siswa membutuhkan bimbingan tambahan pada materi ini.' : null,
                    'status'             => 'terkirim',
                ]);
            }
        }

        // ===== PENILAIAN DATA =====
        $siswaSd = ['Aldi Pratama', 'Budi Santoso', 'Citra Dewi', 'Dian Rahayu', 'Eko Wahyudi'];
        $siswaMts = ['Farhan Al-Ghazali', 'Gita Nuraini', 'Hendra Wijaya', 'Indah Permata', 'Joko Susilo'];
        $siswaSmk = ['Kevin Andrean', 'Laila Fitriani', 'Mirza Ananda', 'Nurul Hidayah', 'Oscar Pranata'];

        foreach ($sdGurus->take(2) as $guru) {
            foreach ($siswaSd as $siswa) {
                foreach (['Ulangan Harian', 'UTS'] as $jenis) {
                    \App\Models\Penilaian::create([
                        'guru_id'          => $guru->id,
                        'sekolah_id'       => $guru->sekolah_id,
                        'nama_siswa'       => $siswa,
                        'nis'              => '2025' . rand(1000, 9999),
                        'kelas'            => 'Kelas IV-A',
                        'mata_pelajaran'   => $guru->spesialisasi,
                        'jenis_penilaian'  => $jenis,
                        'nilai'            => rand(65, 98),
                        'tahun_ajaran'     => '2025/2026',
                        'semester'         => 'Ganjil',
                        'tanggal_penilaian'=> Carbon::now()->subDays(rand(5, 30)),
                    ]);
                }
            }
        }

        foreach ($mtsGurus->take(2) as $guru) {
            foreach ($siswaMts as $siswa) {
                \App\Models\Penilaian::create([
                    'guru_id'          => $guru->id,
                    'sekolah_id'       => $guru->sekolah_id,
                    'nama_siswa'       => $siswa,
                    'nis'              => '2025' . rand(1000, 9999),
                    'kelas'            => 'VII-A',
                    'mata_pelajaran'   => $guru->spesialisasi,
                    'jenis_penilaian'  => 'Ulangan Harian',
                    'nilai'            => rand(70, 95),
                    'tahun_ajaran'     => '2025/2026',
                    'semester'         => 'Ganjil',
                    'tanggal_penilaian'=> Carbon::now()->subDays(rand(3, 20)),
                ]);
            }
        }

        // ===== PENGAJUAN IZIN DATA =====
        foreach ($allGurus->take(5) as $guru) {
            $kepsekId = match($guru->jenjang) {
                'SD'  => $kepsekSdId,
                'MTs' => $kepsekMtsId,
                'SMK' => $kepsekSmkId,
            };

            // Izin disetujui
            \App\Models\PengajuanIzinCuti::create([
                'guru_id'          => $guru->id,
                'sekolah_id'       => $guru->sekolah_id,
                'jenis_pengajuan'  => 'Izin',
                'tanggal_mulai'    => Carbon::now()->subDays(10),
                'tanggal_selesai'  => Carbon::now()->subDays(10),
                'jumlah_hari'      => 1,
                'alasan'           => 'Menghadiri acara keluarga yang tidak dapat ditinggalkan.',
                'status_approval'  => 'disetujui',
                'disetujui_oleh'   => $kepsekId,
                'catatan_approval' => 'Disetujui. Harap pastikan tugas mengajar digantikan oleh guru piket.',
                'disetujui_at'     => Carbon::now()->subDays(9),
            ]);

            // Pengajuan menunggu
            \App\Models\PengajuanIzinCuti::create([
                'guru_id'          => $guru->id,
                'sekolah_id'       => $guru->sekolah_id,
                'jenis_pengajuan'  => 'Cuti',
                'tanggal_mulai'    => Carbon::now()->addDays(5),
                'tanggal_selesai'  => Carbon::now()->addDays(7),
                'jumlah_hari'      => 3,
                'alasan'           => 'Cuti tahunan untuk keperluan pribadi dan keluarga.',
                'status_approval'  => 'menunggu',
            ]);
        }
    }
}
