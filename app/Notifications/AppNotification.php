<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $type = 'info',
        public ?string $icon = null,
        public ?string $senderName = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
            'icon' => $this->icon ?? $this->resolveDefaultIcon($this->type),
            'sender_name' => $this->senderName,
        ];
    }

    /**
     * Get the array representation of the notification for serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Resolve default Lucide icon name according to notification type.
     */
    protected function resolveDefaultIcon(string $type): string
    {
        return match ($type) {
            'izin' => 'calendar',
            'rpp' => 'file-text',
            'sk_tugas' => 'award',
            'evaluasi' => 'bar-chart-2',
            'success' => 'check-circle',
            'warning' => 'alert-triangle',
            'error' => 'alert-circle',
            default => 'bell',
        };
    }
}
