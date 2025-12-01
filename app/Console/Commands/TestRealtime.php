<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Events\BarangUpdated;
use App\Events\PeminjamanUpdated;
use App\Events\AdminNotification;

class TestRealtime extends Command
{
    protected $signature = 'realtime:test {--type=all : Type of test to run (all|barang|peminjaman|notification)}';

    protected $description = 'Test real-time features by broadcasting sample events';

    public function handle()
    {
        $this->info('🧪 Testing Real-time Features...');
        $this->newLine();

        $type = $this->option('type');

        switch ($type) {
            case 'barang':
                $this->testBarangEvents();
                break;
            case 'peminjaman':
                $this->testPeminjamanEvents();
                break;
            case 'notification':
                $this->testNotifications();
                break;
            case 'all':
            default:
                $this->testBarangEvents();
                $this->testPeminjamanEvents();
                $this->testNotifications();
                break;
        }

        $this->newLine();
        $this->info('✅ Testing completed! Check your browser for real-time updates.');
    }

    private function testBarangEvents()
    {
        $this->info('📦 Testing Barang Real-time Events...');

        // Get first barang or create one
        $barang = Barang::first();
        if (!$barang) {
            $this->warn('No barang found. Please create some barang first.');
            return;
        }

        // Test barang updated event
        $this->line('   Broadcasting barang updated event...');
        broadcast(new BarangUpdated($barang, 'updated'));

        // Test admin notification for barang
        broadcast(new AdminNotification(
            'Test Barang Update',
            "Barang '{$barang->nama}' telah diperbarui dalam mode testing.",
            'info',
            ['barang_id' => $barang->id, 'test' => true]
        ));

        $this->line('   ✅ Barang events broadcasted');
    }

    private function testPeminjamanEvents()
    {
        $this->info('📋 Testing Peminjaman Real-time Events...');

        // Get first peminjaman or create one
        $peminjaman = Peminjaman::first();
        if (!$peminjaman) {
            $this->warn('No peminjaman found. Please create some peminjaman first.');
            return;
        }

        // Test peminjaman updated event
        $this->line('   Broadcasting peminjaman updated event...');
        broadcast(new PeminjamanUpdated($peminjaman, 'status_changed'));

        // Test admin notification for peminjaman
        broadcast(new AdminNotification(
            'Test Peminjaman Update',
            "Peminjaman '{$peminjaman->kode_peminjaman}' telah diperbarui dalam mode testing.",
            'success',
            ['peminjaman_id' => $peminjaman->id, 'test' => true]
        ));

        $this->line('   ✅ Peminjaman events broadcasted');
    }

    private function testNotifications()
    {
        $this->info('🔔 Testing Admin Notifications...');

        // Test different notification types
        $notifications = [
            ['title' => 'Test Success Notification', 'message' => 'Ini adalah test notifikasi sukses.', 'type' => 'success'],
            ['title' => 'Test Warning Notification', 'message' => 'Ini adalah test notifikasi peringatan.', 'type' => 'warning'],
            ['title' => 'Test Error Notification', 'message' => 'Ini adalah test notifikasi error.', 'type' => 'error'],
            ['title' => 'Test Info Notification', 'message' => 'Ini adalah test notifikasi informasi.', 'type' => 'info'],
        ];

        foreach ($notifications as $notification) {
            $this->line("   Broadcasting {$notification['type']} notification...");
            broadcast(new AdminNotification(
                $notification['title'],
                $notification['message'],
                $notification['type'],
                ['test' => true, 'timestamp' => now()]
            ));
            sleep(1); // Small delay between notifications
        }

        $this->line('   ✅ All notification types broadcasted');
    }
}
