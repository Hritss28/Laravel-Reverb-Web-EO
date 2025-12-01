<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $action;

    public function __construct(User $user, string $action = 'updated')
    {
        $this->user = $user;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user-updates'),
            new PrivateChannel('admin-notifications')
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->user->id,
            'action' => $this->action,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'phone' => $this->user->phone,
                'department' => $this->user->department,
                'position' => $this->user->position,
                'updated_at' => $this->user->updated_at->format('Y-m-d H:i:s')
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
    }

    public function broadcastWhen(): bool
    {
        return true;
    }
}
