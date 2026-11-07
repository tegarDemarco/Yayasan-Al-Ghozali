<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\PengajuanIzinCuti;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanIzinReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Sekolah $sekolahSd;

    protected User $kepsek;

    protected User $guruUser;

    protected Guru $guru;

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
        ]);

        $this->kepsek = User::factory()->create([
            'role' => 'kepala_sekolah',
            'jenjang' => 'SD',
            'sekolah_id' => $this->sekolahSd->id,
        ]);

        $this->guruUser = User::factory()->create([
            'role' => 'guru',
            'jenjang' => 'SD',
            'sekolah_id' => $this->sekolahSd->id,
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'sekolah_id' => $this->sekolahSd->id,
            'nip' => '198501012010011001',
            'nuptk' => '1234567890123456',
            'jenjang' => 'SD',
            'spesialisasi' => 'Matematika',
            'status_kepegawaian' => 'GTY',
            'pendidikan_terakhir' => 'S1',
            'tanggal_bergabung' => '2020-01-01',
            'status' => 'aktif',
        ]);
    }

    public function test_kepala_sekolah_can_see_pengajuan_izin_and_action_buttons(): void
    {
        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenis_pengajuan' => 'Izin',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(2)->format('Y-m-d'),
            'jumlah_hari' => 2,
            'alasan' => 'Menghadiri acara keluarga di luar kota',
            'status_approval' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kepsek)->get(route('pengajuan-izin.index'));

        $response->assertStatus(200);
        $response->assertSee($this->guruUser->name);
        $response->assertSee('Menghadiri acara keluarga');
        $response->assertSee('Setujui');
        $response->assertSee('Review');
        $response->assertSee('Detail');
    }

    public function test_kepala_sekolah_can_approve_pengajuan_izin(): void
    {
        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenis_pengajuan' => 'Cuti',
            'tanggal_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(7)->format('Y-m-d'),
            'jumlah_hari' => 3,
            'alasan' => 'Keperluan perpanjangan administrasi keluarga',
            'status_approval' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kepsek)->post(route('pengajuan-izin.approve', $pengajuan), [
            'status_approval' => 'disetujui',
            'catatan_approval' => 'Disetujui. Harap titipkan tugas pada guru piket.',
        ]);

        $response->assertRedirect(route('pengajuan-izin.index'));
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status_approval);
        $this->assertEquals('Disetujui. Harap titipkan tugas pada guru piket.', $pengajuan->catatan_approval);
        $this->assertEquals($this->kepsek->id, $pengajuan->disetujui_oleh);
        $this->assertNotNull($pengajuan->disetujui_at);

        // Assert notification sent to guru
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->guruUser->id,
            'type' => 'App\Notifications\AppNotification',
        ]);
    }

    public function test_kepala_sekolah_can_reject_pengajuan_izin_with_note(): void
    {
        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenis_pengajuan' => 'Izin',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(1)->format('Y-m-d'),
            'jumlah_hari' => 1,
            'alasan' => 'Ada acara mendadak',
            'status_approval' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kepsek)->post(route('pengajuan-izin.approve', $pengajuan), [
            'status_approval' => 'ditolak',
            'catatan_approval' => 'Jadwal ujian semester tidak dapat ditinggalkan.',
        ]);

        $response->assertRedirect(route('pengajuan-izin.index'));

        $pengajuan->refresh();
        $this->assertEquals('ditolak', $pengajuan->status_approval);
        $this->assertEquals('Jadwal ujian semester tidak dapat ditinggalkan.', $pengajuan->catatan_approval);
    }

    public function test_guru_can_cancel_pending_pengajuan(): void
    {
        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenis_pengajuan' => 'Sakit',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->format('Y-m-d'),
            'jumlah_hari' => 1,
            'alasan' => 'Demam dan perlu istirahat dokter',
            'status_approval' => 'menunggu',
        ]);

        $response = $this->actingAs($this->guruUser)->delete(route('pengajuan-izin.destroy', $pengajuan));

        $response->assertRedirect(route('pengajuan-izin.index'));
        $this->assertDatabaseMissing('pengajuan_izin_cutis', ['id' => $pengajuan->id]);
    }

    public function test_guru_cannot_cancel_approved_pengajuan(): void
    {
        $pengajuan = PengajuanIzinCuti::create([
            'guru_id' => $this->guru->id,
            'sekolah_id' => $this->sekolahSd->id,
            'jenis_pengajuan' => 'Sakit',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->format('Y-m-d'),
            'jumlah_hari' => 1,
            'alasan' => 'Demam dan perlu istirahat dokter',
            'status_approval' => 'disetujui',
        ]);

        $response = $this->actingAs($this->guruUser)->delete(route('pengajuan-izin.destroy', $pengajuan));

        $response->assertStatus(403);
        $this->assertDatabaseHas('pengajuan_izin_cutis', ['id' => $pengajuan->id]);
    }
}
