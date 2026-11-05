<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\RppModulAjar;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Sekolah $sekolahSd;

    protected Sekolah $sekolahMts;

    protected User $kepsek;

    protected User $pengurus;

    protected Guru $guru1;

    protected Guru $guru2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekolahSd = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '10000001',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-111111',
            'email' => 'sd@alghozaly.sch.id',
            'kepala_sekolah' => 'Kepsek SD',
        ]);

        $this->sekolahMts = Sekolah::create([
            'nama_sekolah' => 'MTs Al-Ghozaly',
            'jenjang' => 'MTs',
            'npsn' => '10000002',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-222222',
            'email' => 'mts@alghozaly.sch.id',
            'kepala_sekolah' => 'Kepsek MTs',
        ]);

        $this->kepsek = User::factory()->create([
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $this->sekolahSd->id,
        ]);

        $this->pengurus = User::factory()->create([
            'role' => 'pengurus_yayasan',
        ]);

        $guruUser1 = User::factory()->create(['name' => 'Ahmad Dahlan', 'role' => 'guru', 'jenjang' => 'SD', 'sekolah_id' => $this->sekolahSd->id]);
        $this->guru1 = Guru::create([
            'user_id' => $guruUser1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'nip' => '11111',
            'nuptk' => '11111',
            'jenjang' => 'SD',
            'spesialisasi' => 'Matematika',
            'status_kepegawaian' => 'GTY',
            'pendidikan_terakhir' => 'S1',
            'tanggal_bergabung' => '2020-01-01',
            'status' => 'aktif',
        ]);

        $guruUser2 = User::factory()->create(['name' => 'Budi Utomo', 'role' => 'guru', 'jenjang' => 'SD', 'sekolah_id' => $this->sekolahSd->id]);
        $this->guru2 = Guru::create([
            'user_id' => $guruUser2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'nip' => '22222',
            'nuptk' => '22222',
            'jenjang' => 'SD',
            'spesialisasi' => 'IPA',
            'status_kepegawaian' => 'GTY',
            'pendidikan_terakhir' => 'S1',
            'tanggal_bergabung' => '2021-01-01',
            'status' => 'aktif',
        ]);
    }

    public function test_jadwal_mengajar_filter_by_hari(): void
    {
        JadwalMengajar::create([
            'guru_id' => $this->guru1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'Matematika',
            'kelas' => 'IV-A',
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:30',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        JadwalMengajar::create([
            'guru_id' => $this->guru2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'IPA Terpadu',
            'kelas' => 'IV-B',
            'hari' => 'Selasa',
            'jam_mulai' => '08:30',
            'jam_selesai' => '10:00',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->kepsek)->get(route('jadwal-mengajar.index', ['hari' => 'Senin']));
        $response->assertStatus(200);
        $response->assertSee('Matematika');
        $response->assertDontSee('IPA Terpadu');
    }

    public function test_jadwal_mengajar_filter_by_guru(): void
    {
        JadwalMengajar::create([
            'guru_id' => $this->guru1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'Matematika',
            'kelas' => 'IV-A',
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:30',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        JadwalMengajar::create([
            'guru_id' => $this->guru2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'IPA Terpadu',
            'kelas' => 'IV-B',
            'hari' => 'Selasa',
            'jam_mulai' => '08:30',
            'jam_selesai' => '10:00',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->kepsek)->get(route('jadwal-mengajar.index', ['guru_id' => $this->guru1->id]));
        $response->assertStatus(200);
        $response->assertSee('Matematika');
        $response->assertDontSee('IPA Terpadu');
    }

    public function test_jadwal_mengajar_filter_by_search_keyword(): void
    {
        JadwalMengajar::create([
            'guru_id' => $this->guru1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'Fisika Dasar',
            'kelas' => 'Kelas-VI-X',
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:30',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        JadwalMengajar::create([
            'guru_id' => $this->guru2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'mata_pelajaran' => 'Kimia Terapan',
            'kelas' => 'Kelas-V-Y',
            'hari' => 'Selasa',
            'jam_mulai' => '08:30',
            'jam_selesai' => '10:00',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->kepsek)->get(route('jadwal-mengajar.index', ['search' => 'Fisika']));
        $response->assertStatus(200);
        $response->assertSee('Fisika Dasar');
        $response->assertDontSee('Kimia Terapan');
    }

    public function test_rpp_filter_by_status_and_tipe(): void
    {
        RppModulAjar::create([
            'guru_id' => $this->guru1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'judul' => 'RPP Matematika Disetujui',
            'mata_pelajaran' => 'Matematika',
            'kelas' => 'IV-A',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'tipe' => 'RPP',
            'status_validasi' => 'disetujui',
        ]);

        RppModulAjar::create([
            'guru_id' => $this->guru2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'judul' => 'Modul Ajar IPA Menunggu',
            'mata_pelajaran' => 'IPA',
            'kelas' => 'IV-B',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'tipe' => 'Modul Ajar',
            'status_validasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kepsek)->get(route('rpp.index', ['status' => 'disetujui']));
        $response->assertStatus(200);
        $response->assertSee('RPP Matematika Disetujui');
        $response->assertDontSee('Modul Ajar IPA Menunggu');

        $responseTipe = $this->actingAs($this->kepsek)->get(route('rpp.index', ['tipe' => 'Modul Ajar']));
        $responseTipe->assertStatus(200);
        $responseTipe->assertSee('Modul Ajar IPA Menunggu');
        $responseTipe->assertDontSee('RPP Matematika Disetujui');
    }

    public function test_rpp_filter_by_guru_and_search(): void
    {
        RppModulAjar::create([
            'guru_id' => $this->guru1->id,
            'sekolah_id' => $this->sekolahSd->id,
            'judul' => 'Perangkat Pembelajaran Aljabar',
            'mata_pelajaran' => 'Matematika',
            'kelas' => 'IV-A',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'tipe' => 'RPP',
            'status_validasi' => 'disetujui',
        ]);

        RppModulAjar::create([
            'guru_id' => $this->guru2->id,
            'sekolah_id' => $this->sekolahSd->id,
            'judul' => 'Perangkat Pembelajaran Biologi',
            'mata_pelajaran' => 'Biologi',
            'kelas' => 'IV-B',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'tipe' => 'Modul Ajar',
            'status_validasi' => 'menunggu',
        ]);

        // Filter by guru_id
        $responseGuru = $this->actingAs($this->kepsek)->get(route('rpp.index', ['guru_id' => $this->guru1->id]));
        $responseGuru->assertStatus(200);
        $responseGuru->assertSee('Perangkat Pembelajaran Aljabar');
        $responseGuru->assertDontSee('Perangkat Pembelajaran Biologi');

        // Filter by search keyword
        $responseSearch = $this->actingAs($this->kepsek)->get(route('rpp.index', ['search' => 'Biologi']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Perangkat Pembelajaran Biologi');
        $responseSearch->assertDontSee('Perangkat Pembelajaran Aljabar');
    }
}
