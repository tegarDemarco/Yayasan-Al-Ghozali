<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Sekolah $sekolahSd;
    protected Sekolah $sekolahMts;
    protected User $kepsekSd;
    protected User $guruUserSd;
    protected Guru $guruSd;
    protected User $guruUserMts;
    protected Guru $guruMts;

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
            'kepala_sekolah' => 'Drs. Ahmad Kusuma, M.Pd',
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

        $this->kepsekSd = User::factory()->create([
            'name' => 'Drs. Ahmad Kusuma, M.Pd',
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $this->sekolahSd->id,
        ]);

        $this->guruUserSd = User::factory()->create([
            'name' => 'Budi Hartono',
            'email' => 'budi.hartono@alghozaly.sch.id',
            'role' => 'guru',
            'jenjang' => 'SD',
            'sekolah_id' => $this->sekolahSd->id,
        ]);

        $this->guruSd = Guru::create([
            'user_id' => $this->guruUserSd->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenjang' => 'SD',
            'spesialisasi' => 'IPA',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);

        $this->guruUserMts = User::factory()->create([
            'name' => 'Guru MTs',
            'email' => 'guru.mts@alghozaly.sch.id',
            'role' => 'guru',
            'jenjang' => 'MTs',
            'sekolah_id' => $this->sekolahMts->id,
        ]);

        $this->guruMts = Guru::create([
            'user_id' => $this->guruUserMts->id,
            'sekolah_id' => $this->sekolahMts->id,
            'jenjang' => 'MTs',
            'spesialisasi' => 'Matematika',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);
    }

    public function test_kepala_sekolah_can_view_edit_page_for_guru_in_same_unit(): void
    {
        $response = $this->actingAs($this->kepsekSd)->get(route('guru.edit', $this->guruSd));

        $response->assertStatus(200);
        $response->assertSee('Edit Guru: Budi Hartono');
        $response->assertSee('budi.hartono@alghozaly.sch.id');
    }

    public function test_kepala_sekolah_cannot_edit_guru_from_another_unit(): void
    {
        $response = $this->actingAs($this->kepsekSd)->get(route('guru.edit', $this->guruMts));

        $response->assertStatus(403);
    }

    public function test_kepala_sekolah_can_update_guru_in_same_unit(): void
    {
        $response = $this->actingAs($this->kepsekSd)->put(route('guru.update', $this->guruSd), [
            'name' => 'Budi Hartono, S.Pd',
            'email' => 'budi.hartono@alghozaly.sch.id',
            'sekolah_id' => $this->sekolahSd->id,
            'spesialisasi' => 'Ilmu Pengetahuan Alam',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('guru.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->guruUserSd->id,
            'name' => 'Budi Hartono, S.Pd',
        ]);
        $this->assertDatabaseHas('gurus', [
            'id' => $this->guruSd->id,
            'spesialisasi' => 'Ilmu Pengetahuan Alam',
        ]);
    }

    public function test_kepala_sekolah_cannot_update_guru_from_another_unit(): void
    {
        $response = $this->actingAs($this->kepsekSd)->put(route('guru.update', $this->guruMts), [
            'name' => 'Hacked MTs',
            'email' => 'guru.mts@alghozaly.sch.id',
            'sekolah_id' => $this->sekolahMts->id,
            'spesialisasi' => 'Matematika',
            'status_kepegawaian' => 'GTY',
            'status' => 'aktif',
        ]);

        $response->assertStatus(403);
    }

    public function test_guru_role_cannot_edit_any_guru(): void
    {
        $response = $this->actingAs($this->guruUserSd)->get(route('guru.edit', $this->guruSd));
        $response->assertStatus(403);
    }
}

