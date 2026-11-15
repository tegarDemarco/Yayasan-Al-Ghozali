<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    public array $data;

    public function __construct(
        array $data = [],
        ?string $title = null,
        ?string $message = null,
        ?string $url = null,
        ?string $type = null,
        ?string $icon = null,
        ?string $senderName = null
    ) {
        $this->data = array_merge($data, array_filter([
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'type' => $type,
            'icon' => $icon,
            'senderName' => $senderName,
        ], fn ($value) => $value !== null));
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Notifikasi Baru',
            'message' => $this->data['message'] ?? '',
            'url' => $this->data['url'] ?? '#',
            'type' => $this->data['type'] ?? 'info',
            'icon' => $this->data['icon'] ?? 'bell',
            'sender_name' => $this->data['senderName'] ?? 'System',
        ];
    }
}
