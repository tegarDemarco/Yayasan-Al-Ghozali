<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    
    public array $data; 

    
    public function __construct(array $data) 
    {
        $this->data = $data;
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