<?php

namespace App\Events;

use App\Models\Barang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarangUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Barang $barang;
    public string $action;

    public function __construct(Barang $barang, string $action = 'updated')
    {
        $this->barang = $barang;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('barang-updates'),
            new PrivateChannel('admin-notifications')
        ];
    }

    public function broadcastAs(): string
    {
        return match($this->action) {
            'created' => 'barang.created',
            'deleted' => 'barang.deleted',
            default => 'barang.updated'
        };
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->barang->id,
            'action' => $this->action,
            'barang' => [
                'id' => $this->barang->id,
                'nama' => $this->barang->nama,
                'kode_barang' => $this->barang->kode_barang,
                'stok' => $this->barang->stok,
                'stok_tersedia' => $this->barang->stok_tersedia,
                'kondisi' => $this->barang->kondisi,
                'tersedia' => $this->barang->tersedia,
                'kategori_nama' => $this->barang->kategori?->nama,
                'harga_sewa_per_hari' => $this->barang->harga_sewa_per_hari,
                'biaya_deposit' => $this->barang->biaya_deposit,
                'lokasi' => $this->barang->lokasi,
                'foto' => $this->barang->foto,
                'updated_at' => $this->barang->updated_at->format('Y-m-d H:i:s')
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
    }

    public function broadcastWhen(): bool
    {
        return true;
    }
}
