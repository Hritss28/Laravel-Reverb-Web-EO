<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $title;
    public string $message;
    public string $type;
    public array $data;

    public function __construct(string $title, string $message, string $type = 'info', array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type; // 'info', 'success', 'warning', 'error'
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications')
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.notification';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'id' => uniqid()
        ];
    }

    public function broadcastWhen(): bool
    {
        return true;
    }
}
