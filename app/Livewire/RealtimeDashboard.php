<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Events\TestEvent;

#[Layout('layouts.livewire-app')]
class RealtimeDashboard extends Component
{
    // Livewire properties
    public array $realtimeStats = [];
    public bool $isConnected = false;
    public array $recentActivity = [];
    public int $connectionStatus = 0; // 0=disconnected, 1=connecting, 2=connected
    public string $lastUpdateTime = '';
    public int $refreshInterval = 30; // seconds
    public bool $autoRefresh = true;

    protected $listeners = [
        'echo:barang-updates,barang.updated' => 'handleBarangUpdate',
        'echo:barang-updates,barang.created' => 'handleBarangCreate',
        'echo:barang-updates,barang.deleted' => 'handleBarangDelete',
        'echo:peminjaman-updates,peminjaman.updated' => 'handlePeminjamanUpdate',
        'echo:peminjaman-updates,peminjaman.created' => 'handlePeminjamanCreate',
        'echo:peminjaman-updates,peminjaman.deleted' => 'handlePeminjamanDelete',
        'echo:peminjaman-updates,peminjaman.status_changed' => 'handlePeminjamanStatusChange',
        'echo:user-updates,user.updated' => 'handleUserUpdate',
        'echo:kategori-updates,kategori.updated' => 'handleKategoriUpdate',
        'echo:test-channel,TestEvent' => 'handleTestEvent',
        'reverb-connected' => 'handleReverbConnected',
        'reverb-disconnected' => 'handleReverbDisconnected',
        'refresh-stats' => 'loadRealtimeStats',
    ];

    public function mount(): void
    {
        $this->loadRealtimeStats();
        $this->loadRecentActivity();
        $this->lastUpdateTime = now()->format('H:i:s');
    }

    // Specific event handlers for better control
    public function handleBarangUpdate($event): void
    {
        $nama = $event['data']['nama'] ?? 'Unknown';
        $this->addActivityItem('barang', 'Barang Diperbarui', 
            "Barang {$nama} telah diperbarui", $event);
        $this->refreshStats('barang_updated');
        $this->showNotification('Barang diperbarui: ' . $nama, 'info');
    }

    public function handleBarangCreate($event): void
    {
        $nama = $event['data']['nama'] ?? 'Unknown';
        $this->addActivityItem('barang', 'Barang Baru', 
            "Barang baru {$nama} ditambahkan", $event);
        $this->refreshStats('barang_created');
        $this->showNotification('Barang baru ditambahkan: ' . $nama, 'success');
    }

    public function handleBarangDelete($event): void
    {
        $nama = $event['data']['nama'] ?? 'Unknown';
        $this->addActivityItem('barang', 'Barang Dihapus', 
            "Barang {$nama} telah dihapus", $event);
        $this->refreshStats('barang_deleted');
        $this->showNotification('Barang dihapus: ' . $nama, 'warning');
    }

    public function handlePeminjamanUpdate($event): void
    {
        $kode = $event['data']['kode_peminjaman'] ?? 'Unknown';
        $this->addActivityItem('peminjaman', 'Peminjaman Diperbarui', 
            "Peminjaman {$kode} diperbarui", $event);
        $this->refreshStats('peminjaman_updated');
        $this->showNotification('Peminjaman diperbarui: ' . $kode, 'info');
    }

    public function handlePeminjamanCreate($event): void
    {
        $kode = $event['data']['kode_peminjaman'] ?? 'Unknown';
        $userName = $event['data']['user_name'] ?? 'Unknown';
        $this->addActivityItem('peminjaman', 'Peminjaman Baru', 
            "Peminjaman baru {$kode} dibuat oleh {$userName}", $event);
        $this->refreshStats('peminjaman_created');
        
        // Show notification for new peminjaman
        $this->showNotification("Peminjaman baru {$kode} menunggu persetujuan", 'warning');
    }

    public function handlePeminjamanDelete($event): void
    {
        $kode = $event['data']['kode_peminjaman'] ?? 'Unknown';
        $this->addActivityItem('peminjaman', 'Peminjaman Dihapus', 
            "Peminjaman {$kode} telah dihapus", $event);
        $this->refreshStats('peminjaman_deleted');
        $this->showNotification('Peminjaman dihapus: ' . $kode, 'error');
    }

    public function handlePeminjamanStatusChange($event): void
    {
        $status = $event['data']['status'] ?? 'unknown';
        $kode = $event['data']['kode_peminjaman'] ?? 'Unknown';
        $this->addActivityItem('peminjaman', 'Status Berubah', 
            "Status peminjaman {$kode} berubah menjadi {$status}", $event);
        $this->refreshStats('peminjaman_status_changed');
        $this->showNotification("Status peminjaman {$kode}: {$status}", 'info');
    }

    public function handleUserUpdate($event): void
    {
        $userName = $event['data']['name'] ?? 'Unknown';
        $this->addActivityItem('user', 'User Diperbarui', 
            "Data user {$userName} diperbarui", $event);
        $this->refreshStats('user_updated');
    }

    public function handleKategoriUpdate($event): void
    {
        $nama = $event['data']['nama'] ?? 'Unknown';
        $this->addActivityItem('kategori', 'Kategori Diperbarui', 
            "Kategori {$nama} diperbarui", $event);
        $this->refreshStats('kategori_updated');
    }

    public function handleTestEvent($event): void
    {
        $this->addActivityItem('test', 'Test Event', 
            $event['message'] ?? 'Test event received', $event);
        
        $this->showNotification($event['message'] ?? 'Real-time connection working', 'success');
    }

    public function handleReverbConnected(): void
    {
        $this->connectionStatus = 2;
        $this->isConnected = true;
        $this->addActivityItem('system', 'Terhubung', 'Real-time connection established', []);
        $this->showNotification('WebSocket connection established', 'success');
    }

    public function handleReverbDisconnected(): void
    {
        $this->connectionStatus = 0;
        $this->isConnected = false;
        $this->addActivityItem('system', 'Terputus', 'Real-time connection lost', []);
        $this->showNotification('Real-time connection disconnected', 'error');
    }

    private function addActivityItem(string $type, string $title, string $message, array $event): void
    {
        array_unshift($this->recentActivity, [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'timestamp' => now(),
            'data' => $event['data'] ?? []
        ]);

        // Keep only last 15 activities
        $this->recentActivity = array_slice($this->recentActivity, 0, 15);
        $this->lastUpdateTime = now()->format('H:i:s');
    }

    private function refreshStats(string $eventType): void
    {
        $this->loadRealtimeStats();
        $this->lastUpdateTime = now()->format('H:i:s');
        
        // Dispatch browser event for additional JS handling
        $this->dispatch('stats-refreshed', [
            'eventType' => $eventType,
            'timestamp' => now()->timestamp
        ]);
    }

    public function loadRealtimeStats(): void
    {
        $this->realtimeStats = [
            'total_barang' => \App\Models\Barang::count(),
            'barang_stok_rendah' => \App\Models\Barang::where('stok', '<=', 5)->count(),
            'barang_tersedia' => \App\Models\Barang::where('stok', '>', 0)->count(),
            'peminjaman_pending' => \App\Models\Peminjaman::where('status', 'pending')->count(),
            'peminjaman_aktif' => \App\Models\Peminjaman::where('status', 'dipinjam')->count(),
            'peminjaman_terlambat' => \App\Models\Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_kembali_rencana', '<', now())->count(),
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'peminjaman_hari_ini' => \App\Models\Peminjaman::whereDate('created_at', today())->count(),
            'peminjaman_disetujui' => \App\Models\Peminjaman::where('status', 'disetujui')->count(),
            'last_updated' => now()->format('H:i:s')
        ];
    }

    public function loadRecentActivity(): void
    {
        $this->recentActivity = collect([
            // Recent peminjaman
            ...\App\Models\Peminjaman::with(['user', 'barang'])
                ->latest()
                ->take(8)
                ->get()
                ->map(fn($p) => [
                    'type' => 'peminjaman',
                    'title' => 'Peminjaman ' . ucfirst($p->status),
                    'message' => "Peminjaman {$p->kode_peminjaman} oleh {$p->user?->name} - {$p->barang?->nama}",
                    'timestamp' => $p->created_at,
                    'data' => ['id' => $p->id, 'status' => $p->status]
                ]),
            
            // Recent barang updates
            ...\App\Models\Barang::latest('updated_at')
                ->take(5)
                ->get()
                ->map(fn($b) => [
                    'type' => 'barang',
                    'title' => 'Barang Diperbarui',
                    'message' => "Barang {$b->nama} - Stok: {$b->stok}",
                    'timestamp' => $b->updated_at,
                    'data' => ['id' => $b->id, 'stok' => $b->stok]
                ])
        ])
        ->sortByDesc('timestamp')
        ->take(15)
        ->values()
        ->toArray();
    }

    public function refreshData(): void
    {
        $this->loadRealtimeStats();
        $this->loadRecentActivity();
        $this->lastUpdateTime = now()->format('H:i:s');
        $this->showNotification('Dashboard data manually refreshed', 'info');
    }

    public function testConnection(): void
    {
        try {
            // Broadcast test event
            broadcast(new TestEvent([
                'message' => 'Test connection from dashboard at ' . now()->format('H:i:s'),
                'timestamp' => now()->toISOString(),
                'user' => \Illuminate\Support\Facades\Auth::user()?->name ?? 'System',
                'source' => 'livewire_dashboard'
            ]));
            
            $this->addActivityItem('test', 'Test Sent', 'Test broadcast event dispatched', [
                'timestamp' => now(),
                'status' => 'sent'
            ]);
            
            $this->showNotification('Broadcasting test event to all connected clients', 'info');
                
        } catch (\Exception $e) {
            $this->addActivityItem('error', 'Test Failed', 'Failed to send test: ' . $e->getMessage(), [
                'error' => $e->getMessage()
            ]);
            
            $this->showNotification('Test failed: ' . $e->getMessage(), 'error');
        }
    }

    public function clearActivity(): void
    {
        $this->recentActivity = [];
        $this->showNotification('Recent activity log has been cleared', 'success');
    }

    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
        $status = $this->autoRefresh ? 'enabled' : 'disabled';
        $this->showNotification("Auto refresh {$status}", 'info');
    }

    public function toggleConnection(): void
    {
        $this->isConnected = !$this->isConnected;
        $this->connectionStatus = $this->isConnected ? 2 : 0;
        
        $status = $this->isConnected ? 'connected' : 'disconnected';
        $this->showNotification("Connection manually {$status}", 'info');
        
        // Dispatch to JavaScript
        $this->dispatch('connection-toggled', ['connected' => $this->isConnected]);
    }

    private function showNotification(string $message, string $type = 'info'): void
    {
        $this->dispatch('show-notification', [
            'message' => $message,
            'type' => $type,
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    public function getActivityIcon(string $type): string
    {
        return match($type) {
            'peminjaman' => '📋',
            'barang' => '📦',
            'user' => '👤',
            'kategori' => '🏷️',
            'test' => '🧪',
            'system' => '⚙️',
            'error' => '❌',
            default => '📢'
        };
    }

    public function getActivityColor(string $type): string
    {
        return match($type) {
            'peminjaman' => 'text-blue-600',
            'barang' => 'text-green-600',
            'user' => 'text-purple-600',
            'kategori' => 'text-orange-600',
            'test' => 'text-indigo-600',
            'system' => 'text-gray-600',
            'error' => 'text-red-600',
            default => 'text-gray-500'
        };
    }

    public function render()
    {
        return view('livewire.realtime-dashboard');
    }
}
