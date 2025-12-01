<?php

namespace App\Events;

use App\Models\Kategori;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KategoriUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Kategori $kategori;
    public string $action;

    public function __construct(Kategori $kategori, string $action = 'updated')
    {
        $this->kategori = $kategori;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('kategori-updates'),
            new PrivateChannel('admin-notifications')
        ];
    }

    public function broadcastAs(): string
    {
        return 'kategori.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->kategori->id,
            'action' => $this->action,
            'kategori' => [
                'id' => $this->kategori->id,
                'nama' => $this->kategori->nama,
                'kode' => $this->kategori->kode,
                'deskripsi' => $this->kategori->deskripsi,
                'updated_at' => $this->kategori->updated_at->format('Y-m-d H:i:s')
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
    }

    public function broadcastWhen(): bool
    {
        return true;
    }
}
