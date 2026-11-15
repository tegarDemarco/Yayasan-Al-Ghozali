<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalMengajarNavigationFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Sekolah $sekolah;
    protected User $pengurus;
    protected User $guruUser;
    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekolah = Sekolah::create([
            'nama_sekolah' => 'SMK Al-Ghozaly',
            'jenjang' => 'SMK',
            'npsn' => '10000003',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-333333',
            'email' => 'smk@alghozaly.sch.id',
            'kepala_sekolah' => 'H. Bambang Sutrisno, S.T., M.T',
        ]);

        $this->pengurus = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'pengurus_yayasan',
        ]);

        $this->guruUser = User::factory()->create([
            'name' => 'Lestari Wahyuni',
            'role' => 'guru',
            'jenjang' => 'SMK',
            'sekolah_id' => $this->sekolah->id,
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'sekolah_id' => $this->sekolah->id,
            'nip' => '198701012020121002',
            'nuptk' => '9876543210123458',
            'jenjang' => 'SMK',
            'spesialisasi' => 'Teknik Komputer & Jaringan',
            'status_kepegawaian' => 'GTT',
            'status' => 'aktif',
        ]);
    }

    public function test_detail_guru_has_link_to_jadwal_mengajar_with_guru_id(): void
    {
        $response = $this->actingAs($this->pengurus)->get(route('guru.show', $this->guru));

        $response->assertStatus(200);
        $response->assertSee(route('jadwal-mengajar.index', ['guru_id' => $this->guru->id]));
    }

    public function test_jadwal_mengajar_shows_kembali_ke_detail_guru_when_guru_id_provided(): void
    {
        $response = $this->actingAs($this->pengurus)->get(route('jadwal-mengajar.index', ['guru_id' => $this->guru->id]));

        $response->assertStatus(200);
        $response->assertSee('Kembali ke Detail Guru');
        $response->assertSee(route('guru.show', $this->guru->id));
        $response->assertSee('Menampilkan jadwal mengajar untuk:');
        $response->assertSee('Lestari Wahyuni');
    }

    public function test_jadwal_mengajar_pagination_renders_user_friendly_previous_and_next(): void
    {
        // Create 18 schedules so it paginates (per page is 15)
        for ($i = 1; $i <= 18; $i++) {
            JadwalMengajar::create([
                'sekolah_id' => $this->sekolah->id,
                'guru_id' => $this->guru->id,
                'hari' => 'Senin',
                'jam_mulai' => sprintf('%02d:00', ($i % 12) + 7),
                'jam_selesai' => sprintf('%02d:45', ($i % 12) + 7),
                'mata_pelajaran' => 'Mapel ' . $i,
                'kelas' => 'X-A',
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
                'is_aktif' => true,
            ]);
        }

        $response = $this->actingAs($this->pengurus)->get(route('jadwal-mengajar.index'));

        $response->assertStatus(200);
        // Page 1 should show disabled previous and active next
        $response->assertSee('← Sebelumnya');
        $response->assertSee('Selanjutnya →');
        $response->assertSee('Menampilkan 1–15 dari 18 data jadwal');

        // Check page 2
        $responsePage2 = $this->actingAs($this->pengurus)->get(route('jadwal-mengajar.index', ['page' => 2]));
        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('← Sebelumnya');
        $responsePage2->assertSee('Selanjutnya →');
        $responsePage2->assertSee('Menampilkan 16–18 dari 18 data jadwal');
    }
}

