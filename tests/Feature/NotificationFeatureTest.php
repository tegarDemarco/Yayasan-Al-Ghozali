<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login when visiting notifikasi page.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('notifikasi.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can view the notification page.
     */
    public function test_authenticated_user_can_view_notification_page(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_user']);

        $user->notify(new AppNotification(
            title: 'RPP Validasi',
            message: 'RPP Anda telah disetujui',
            url: '/rpp',
            type: 'rpp',
            icon: 'file-text',
            senderName: 'Kepala Sekolah'
        ));

        $response = $this->actingAs($user)->get(route('notifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
        $response->assertSee('RPP Validasi');
        $response->assertSee('RPP Anda telah disetujui');
    }

    /**
     * Test filtering unread notifications.
     */
    public function test_user_can_filter_unread_notifications(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_unread']);

        $user->notify(new AppNotification(
            title: 'Notif Unread',
            message: 'Pesan belum dibaca',
        ));

        $user->notify(new AppNotification(
            title: 'Notif Read',
            message: 'Pesan sudah dibaca',
        ));

        // Mark second notification as read
        $readNotification = $user->notifications()->where('data->title', 'Notif Read')->first();
        $readNotification->markAsRead();

        $response = $this->actingAs($user)->get(route('notifikasi.index', ['filter' => 'unread']));

        $response->assertStatus(200);
        $response->assertSee('Notif Unread');
        $this->assertEquals(1, $response->viewData('notifications')->total());
        $this->assertEquals('Notif Unread', $response->viewData('notifications')->first()->data['title']);
    }

    /**
     * Test user can read and redirect to notification target url.
     */
    public function test_user_can_read_and_redirect_notification(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_redirect']);

        $user->notify(new AppNotification(
            title: 'Izin Disetujui',
            message: 'Pengajuan izin disetujui',
            url: route('pengajuan-izin.index'),
            type: 'izin',
        ));

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->get(route('notifikasi.read-and-redirect', $notification->id));

        $response->assertRedirect(route('pengajuan-izin.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * Test user can mark a single notification as read via POST.
     */
    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_mark']);

        $user->notify(new AppNotification(
            title: 'Judul Notifikasi',
            message: 'Isi Notifikasi',
        ));

        $notification = $user->unreadNotifications()->first();

        $response = $this->actingAs($user)->post(route('notifikasi.mark-as-read', $notification->id));

        $response->assertStatus(302);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * Test user can mark all unread notifications as read.
     */
    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_mark_all']);

        $user->notify(new AppNotification(title: 'Notif 1', message: 'Isi 1'));
        $user->notify(new AppNotification(title: 'Notif 2', message: 'Isi 2'));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifikasi.mark-all-read'));

        $response->assertStatus(302);
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    /**
     * Test user can delete a notification.
     */
    public function test_user_can_delete_notification(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'guru_del']);

        $user->notify(new AppNotification(title: 'Notif Dihapus', message: 'Akan dihapus'));
        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->delete(route('notifikasi.destroy', $notification->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    /**
     * Test user cannot access or delete other user's notification.
     */
    public function test_user_cannot_access_other_users_notification(): void
    {
        $userA = User::factory()->create(['role' => 'guru', 'username' => 'user_a']);
        $userB = User::factory()->create(['role' => 'guru', 'username' => 'user_b']);

        $userA->notify(new AppNotification(title: 'Notif Rahasia', message: 'Pesan A'));
        $notificationA = $userA->notifications()->first();

        $response = $this->actingAs($userB)->get(route('notifikasi.read-and-redirect', $notificationA->id));
        $response->assertStatus(404);

        $deleteResponse = $this->actingAs($userB)->delete(route('notifikasi.destroy', $notificationA->id));
        $deleteResponse->assertStatus(404);
    }

    /**
     * Test unread data JSON endpoint.
     */
    public function test_unread_data_endpoint_returns_json(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'username' => 'user_json']);

        $user->notify(new AppNotification(
            title: 'Notif Json',
            message: 'Pesan Json',
            url: '/test-url',
        ));

        $response = $this->actingAs($user)->getJson(route('notifikasi.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'notifications' => [
                '*' => [
                    'id',
                    'data',
                    'read_at',
                    'created_at_human',
                    'url',
                ],
            ],
        ]);
        $response->assertJson([
            'unread_count' => 1,
        ]);
    }
}
