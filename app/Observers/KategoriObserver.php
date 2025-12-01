<?php

namespace App\Observers;

use App\Models\Kategori;
use App\Events\KategoriUpdated;
use App\Events\AdminNotification;

class KategoriObserver
{
    public function created(Kategori $kategori): void
    {
        // Broadcast event for real-time updates
        broadcast(new KategoriUpdated($kategori, 'created'));

        // Send admin notification
        broadcast(new AdminNotification(
            'Kategori Baru Ditambahkan',
            "Kategori baru '{$kategori->nama}' dengan kode '{$kategori->kode}' telah ditambahkan.",
            'success',
            ['kategori_id' => $kategori->id, 'action' => 'created']
        ));
    }

    public function updated(Kategori $kategori): void
    {
        // Broadcast event for real-time updates
        broadcast(new KategoriUpdated($kategori, 'updated'));

        // Send admin notification for important changes
        broadcast(new AdminNotification(
            'Kategori Diperbarui',
            "Kategori '{$kategori->nama}' telah diperbarui.",
            'info',
            ['kategori_id' => $kategori->id, 'action' => 'updated']
        ));
    }

    public function deleted(Kategori $kategori): void
    {
        // Broadcast event for real-time updates
        broadcast(new KategoriUpdated($kategori, 'deleted'));

        // Send admin notification
        broadcast(new AdminNotification(
            'Kategori Dihapus',
            "Kategori '{$kategori->nama}' telah dihapus dari sistem.",
            'warning',
            ['kategori_id' => $kategori->id, 'action' => 'deleted']
        ));
    }
}
