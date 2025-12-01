<?php

namespace App\Observers;

use App\Models\Barang;
use App\Events\BarangUpdated;
use App\Events\AdminNotification;

class BarangObserver
{
    public function created(Barang $barang): void
    {
        // Broadcast specific created event
        broadcast(new BarangUpdated($barang, 'created'))->toOthers();

        // Send admin notification
        broadcast(new AdminNotification(
            'Barang Baru Ditambahkan',
            "Barang '{$barang->nama}' dengan kode '{$barang->kode_barang}' telah ditambahkan ke inventaris.",
            'success',
            ['barang_id' => $barang->id, 'action' => 'created']
        ));
    }

    public function updated(Barang $barang): void
    {
        // Broadcast event for real-time updates
        broadcast(new BarangUpdated($barang, 'updated'));

        // Check if important fields changed
        $importantChanges = [];
        if ($barang->wasChanged('stok')) {
            $importantChanges[] = 'stok';
        }
        if ($barang->wasChanged('kondisi')) {
            $importantChanges[] = 'kondisi';
        }
        if ($barang->wasChanged('tersedia')) {
            $importantChanges[] = 'status ketersediaan';
        }

        if (!empty($importantChanges)) {
            broadcast(new AdminNotification(
                'Barang Diperbarui',
                "Barang '{$barang->nama}' telah diperbarui. Perubahan: " . implode(', ', $importantChanges),
                'info',
                ['barang_id' => $barang->id, 'action' => 'updated', 'changes' => $importantChanges]
            ));
        }
    }

    public function deleted(Barang $barang): void
    {
        // Broadcast event for real-time updates
        broadcast(new BarangUpdated($barang, 'deleted'));

        // Send admin notification
        broadcast(new AdminNotification(
            'Barang Dihapus',
            "Barang '{$barang->nama}' dengan kode '{$barang->kode_barang}' telah dihapus dari inventaris.",
            'warning',
            ['barang_id' => $barang->id, 'action' => 'deleted']
        ));
    }
}
