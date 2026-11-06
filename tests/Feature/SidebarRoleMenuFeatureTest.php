<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarRoleMenuFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_kepala_sekolah_sees_lihat_beban_ajar_and_not_upload_bahan_ajar(): void
    {
        $sekolah = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '10000001',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-111111',
            'email' => 'sd@alghozaly.sch.id',
        ]);

        $kepsek = User::factory()->create([
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $sekolah->id,
        ]);

        $response = $this->actingAs($kepsek)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Lihat Beban Ajar');
        $response->assertDontSee('Upload Bahan Ajar');
        $response->assertSee(route('rekap-beban.index'));
    }

    public function test_guru_sees_upload_bahan_ajar(): void
    {
        $sekolah = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '10000001',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-111111',
            'email' => 'sd@alghozaly.sch.id',
        ]);

        $guru = User::factory()->create([
            'role' => 'guru',
            'jenjang' => 'SD',
            'sekolah_id' => $sekolah->id,
        ]);

        $response = $this->actingAs($guru)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Upload Bahan Ajar');
        $response->assertSee(route('bahan-ajar.index'));
    }

    public function test_kepala_sekolah_can_access_rekap_beban_index(): void
    {
        $sekolah = Sekolah::create([
            'nama_sekolah' => 'SD Al-Ghozaly',
            'jenjang' => 'SD',
            'npsn' => '10000001',
            'akreditasi' => 'A',
            'alamat' => 'Brebes',
            'telepon' => '0283-111111',
            'email' => 'sd@alghozaly.sch.id',
        ]);

        $kepsek = User::factory()->create([
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $sekolah->id,
        ]);

        $response = $this->actingAs($kepsek)->get(route('rekap-beban.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekap Beban Mengajar');
    }
}
