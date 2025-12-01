<?php

namespace App\Events;

use App\Models\Peminjaman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PeminjamanUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Peminjaman $peminjaman;
    public string $action;

    public function __construct(Peminjaman $peminjaman, string $action = 'updated')
    {
        $this->peminjaman = $peminjaman;
        $this->action = $action; // 'created', 'updated', 'deleted', 'status_changed'
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('peminjaman-updates'),
            new PrivateChannel('admin-notifications'),
            new PrivateChannel('user.' . $this->peminjaman->user_id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'peminjaman.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->peminjaman->id,
            'action' => $this->action,
            'peminjaman' => [
                'id' => $this->peminjaman->id,
                'kode_peminjaman' => $this->peminjaman->kode_peminjaman,
                'user_id' => $this->peminjaman->user_id,
                'user_name' => $this->peminjaman->user?->name,
                'barang_id' => $this->peminjaman->barang_id,
                'barang_nama' => $this->peminjaman->barang?->nama,
                'jumlah' => $this->peminjaman->jumlah,
                'status' => $this->peminjaman->status,
                'tanggal_pinjam' => $this->peminjaman->tanggal_pinjam?->format('Y-m-d'),
                'tanggal_kembali_rencana' => $this->peminjaman->tanggal_kembali_rencana?->format('Y-m-d'),
                'tanggal_kembali_aktual' => $this->peminjaman->tanggal_kembali_aktual?->format('Y-m-d'),
                'payment_status' => $this->peminjaman->payment_status,
                'total_pembayaran' => $this->peminjaman->total_pembayaran,
                'keperluan' => $this->peminjaman->keperluan,
                'catatan_admin' => $this->peminjaman->catatan_admin,
                'updated_at' => $this->peminjaman->updated_at->format('Y-m-d H:i:s')
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
    }

    public function broadcastWhen(): bool
    {
        return true;
    }
}
