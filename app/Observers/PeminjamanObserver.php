<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Events\PeminjamanUpdated;
use App\Events\AdminNotification;

class PeminjamanObserver
{
    public function created(Peminjaman $peminjaman): void
    {
        // Broadcast event for real-time updates
        broadcast(new PeminjamanUpdated($peminjaman, 'created'));

        // Send admin notification
        broadcast(new AdminNotification(
            'Peminjaman Baru',
            "Peminjaman baru dari {$peminjaman->user?->name} untuk barang '{$peminjaman->barang?->nama}' menunggu persetujuan.",
            'info',
            ['peminjaman_id' => $peminjaman->id, 'action' => 'created']
        ));
    }

    public function updated(Peminjaman $peminjaman): void
    {
        // Broadcast event for real-time updates
        broadcast(new PeminjamanUpdated($peminjaman, 'updated'));

        // Check for status changes
        if ($peminjaman->wasChanged('status')) {
            $originalStatus = $peminjaman->getOriginal('status');
            $newStatus = $peminjaman->status;
            
            $statusMessages = [
                'pending' => 'menunggu persetujuan',
                'disetujui' => 'disetujui',
                'ditolak' => 'ditolak',
                'dipinjam' => 'sedang dipinjam',
                'dikembalikan' => 'telah dikembalikan',
                'terlambat' => 'terlambat dikembalikan'
            ];

            broadcast(new AdminNotification(
                'Status Peminjaman Berubah',
                "Status peminjaman {$peminjaman->kode_peminjaman} berubah dari {$statusMessages[$originalStatus]} menjadi {$statusMessages[$newStatus]}.",
                $newStatus === 'ditolak' ? 'warning' : ($newStatus === 'disetujui' ? 'success' : 'info'),
                ['peminjaman_id' => $peminjaman->id, 'action' => 'status_changed', 'status' => $newStatus]
            ));

            // Broadcast specific status change event
            broadcast(new PeminjamanUpdated($peminjaman, 'status_changed'));
        }

        // Check for payment status changes
        if ($peminjaman->wasChanged('payment_status')) {
            $paymentStatus = $peminjaman->payment_status;
            $paymentMessages = [
                'pending' => 'menunggu pembayaran',
                'paid' => 'telah dibayar',
                'failed' => 'pembayaran gagal',
                'expired' => 'pembayaran kedaluwarsa'
            ];

            broadcast(new AdminNotification(
                'Status Pembayaran Berubah',
                "Status pembayaran {$peminjaman->kode_peminjaman} menjadi {$paymentMessages[$paymentStatus]}.",
                $paymentStatus === 'paid' ? 'success' : ($paymentStatus === 'failed' ? 'error' : 'warning'),
                ['peminjaman_id' => $peminjaman->id, 'action' => 'payment_updated', 'payment_status' => $paymentStatus]
            ));
        }
    }

    public function deleted(Peminjaman $peminjaman): void
    {
        // Broadcast event for real-time updates
        broadcast(new PeminjamanUpdated($peminjaman, 'deleted'));

        // Send admin notification
        broadcast(new AdminNotification(
            'Peminjaman Dihapus',
            "Peminjaman {$peminjaman->kode_peminjaman} telah dihapus dari sistem.",
            'warning',
            ['peminjaman_id' => $peminjaman->id, 'action' => 'deleted']
        ));
    }
}
