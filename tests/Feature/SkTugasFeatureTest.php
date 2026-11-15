<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\SkTugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkTugasFeatureTest extends TestCase
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
            'name' => 'Ir. Tegar Tri Wibowo, S.Kom, M.Kom',
            'role' => 'guru',
            'jenjang' => 'SMK',
            'sekolah_id' => $this->sekolah->id,
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'sekolah_id' => $this->sekolah->id,
            'nip' => '198701012020121001',
            'nuptk' => '9876543210123456',
            'jenjang' => 'SMK',
            'spesialisasi' => 'Teknik Komputer & Jaringan',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);
    }

    public function test_user_can_view_sk_tugas_detail_with_information_and_keterangan(): void
    {
        $sk = SkTugas::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolah->id,
            'no_sk' => 'SK/004/YAG/SMK/2026',
            'judul_sk' => 'SK Pemberhentian Guru',
            'jenis_sk' => 'Pemberhentian',
            'tanggal_sk' => '2026-11-04',
            'berlaku_mulai' => '2026-11-04',
            'berlaku_sampai' => null,
            'keterangan' => 'Diberhentikan dengan hormat atas permohonan sendiri.',
            'diterbitkan_oleh' => $this->pengurus->id,
        ]);

        $response = $this->actingAs($this->pengurus)->get(route('sk-tugas.show', $sk));

        $response->assertStatus(200);
        $response->assertSee('SK/004/YAG/SMK/2026');
        $response->assertSee('SK Pemberhentian Guru');
        $response->assertSee('Ir. Tegar Tri Wibowo, S.Kom, M.Kom');
        $response->assertSee('SMK');
        $response->assertSee('Pemberhentian');
        $response->assertSee('04/11/2026');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Diberhentikan dengan hormat atas permohonan sendiri.');
    }

    public function test_user_can_view_sk_tugas_detail_when_keterangan_is_empty(): void
    {
        $sk = SkTugas::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolah->id,
            'no_sk' => 'SK/003/YAG/SD/2026',
            'judul_sk' => 'SK Penugasan sebagai Wali Kelas',
            'jenis_sk' => 'Tugas Tambahan',
            'tanggal_sk' => '2026-08-02',
            'berlaku_mulai' => '2025-07-15',
            'berlaku_sampai' => '2026-06-30',
            'keterangan' => null,
            'diterbitkan_oleh' => $this->pengurus->id,
        ]);

        $response = $this->actingAs($this->pengurus)->get(route('sk-tugas.show', $sk));

        $response->assertStatus(200);
        $response->assertSee('SK/003/YAG/SD/2026');
        $response->assertSee('SK Penugasan sebagai Wali Kelas');
        $response->assertSee('Tidak ada keterangan tambahan.');
    }

    public function test_user_can_delete_sk_tugas(): void
    {
        $sk = SkTugas::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolah->id,
            'no_sk' => 'SK/005/YAG/SMK/2026',
            'judul_sk' => 'SK Sanksi Teguran',
            'jenis_sk' => 'Sanksi',
            'tanggal_sk' => '2026-11-05',
            'diterbitkan_oleh' => $this->pengurus->id,
        ]);

        $response = $this->actingAs($this->pengurus)->delete(route('sk-tugas.destroy', $sk));

        $response->assertRedirect(route('sk-tugas.index'));
        $this->assertDatabaseMissing('sk_tugas', ['id' => $sk->id]);
    }

    public function test_pengurus_yayasan_can_see_all_active_gurus_in_create_form(): void
    {
        $response = $this->actingAs($this->pengurus)->get(route('sk-tugas.create'));

        $response->assertStatus(200);
        $response->assertSee('Ir. Tegar Tri Wibowo, S.Kom, M.Kom');
        $response->assertSee('value="'.$this->guru->id.'"', false);
    }

    public function test_guru_cannot_access_create_sk_tugas(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('sk-tugas.create'));

        $response->assertStatus(403);
    }

    public function test_pengurus_yayasan_can_filter_sk_tugas_by_jenjang(): void
    {
        $sekolahSd = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '10000001',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-111111',
            'email' => 'sd@alghozaly.sch.id',
            'kepala_sekolah' => 'Drs. Ahmad Kusuma, M.Pd',
        ]);

        $guruSdUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'role' => 'guru',
            'jenjang' => 'SD',
            'sekolah_id' => $sekolahSd->id,
        ]);

        $guruSd = Guru::create([
            'user_id' => $guruSdUser->id,
            'sekolah_id' => $sekolahSd->id,
            'nip' => '199001012020122001',
            'nuptk' => '9876543210123457',
            'jenjang' => 'SD',
            'spesialisasi' => 'Guru Kelas SD',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);

        $skSmk = SkTugas::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolah->id,
            'no_sk' => 'SK/004/YAG/SMK/2026',
            'judul_sk' => 'SK Guru SMK',
            'jenis_sk' => 'Pengangkatan',
            'tanggal_sk' => '2026-11-04',
            'diterbitkan_oleh' => $this->pengurus->id,
        ]);

        $skSd = SkTugas::create([
            'guru_id' => $guruSd->id,
            'sekolah_id' => $sekolahSd->id,
            'no_sk' => 'SK/003/YAG/SD/2026',
            'judul_sk' => 'SK Guru SD',
            'jenis_sk' => 'Tugas Tambahan',
            'tanggal_sk' => '2026-08-02',
            'diterbitkan_oleh' => $this->pengurus->id,
        ]);

        // Filter SD
        $responseSd = $this->actingAs($this->pengurus)->get(route('sk-tugas.index', ['jenjang' => 'SD']));
        $responseSd->assertStatus(200);
        $responseSd->assertSee('SK/003/YAG/SD/2026');
        $responseSd->assertDontSee('SK/004/YAG/SMK/2026');

        // Filter SMK
        $responseSmk = $this->actingAs($this->pengurus)->get(route('sk-tugas.index', ['jenjang' => 'SMK']));
        $responseSmk->assertStatus(200);
        $responseSmk->assertSee('SK/004/YAG/SMK/2026');
        $responseSmk->assertDontSee('SK/003/YAG/SD/2026');
    }
}

