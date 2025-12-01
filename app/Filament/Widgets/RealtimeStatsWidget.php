<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Kategori;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class RealtimeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null; // Disable auto-polling, use real-time updates

    #[On('echo:barang-updates,barang.updated')]
    #[On('echo:peminjaman-updates,peminjaman.updated')]
    #[On('echo:user-updates,user.updated')]
    #[On('echo:kategori-updates,kategori.updated')]
    public function refreshStats(): void
    {
        // Refresh the component when real-time updates are received
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        // Total Barang dengan status stok
        $totalBarang = Barang::count();
        $barangStokRendah = Barang::where('stok', '<=', 5)->count();
        
        // Peminjaman stats
        $peminjamanPending = Peminjaman::where('status', 'pending')->count();
        $peminjamanAktif = Peminjaman::where('status', 'dipinjam')->count();
        $peminjamanHariIni = Peminjaman::whereDate('created_at', today())->count();
        
        // User stats
        $totalUsers = User::where('role', 'user')->count();
        $usersHariIni = User::whereDate('created_at', today())->count();
        
        // Revenue stats (last 30 days)
        $pendapatanBulanIni = Peminjaman::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_pembayaran');

        return [
            Stat::make('Total Barang', $totalBarang)
                ->description($barangStokRendah > 0 ? "{$barangStokRendah} stok rendah" : 'Semua stok aman')
                ->descriptionIcon($barangStokRendah > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($barangStokRendah > 0 ? 'warning' : 'success')
                ->extraAttributes([
                    'wire:poll.5s' => '',
                    'class' => 'real-time-stat'
                ]),

            Stat::make('Peminjaman Pending', $peminjamanPending)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-o-clock')
                ->color($peminjamanPending > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.peminjaman.index', ['tableFilters[status][value]' => 'pending']))
                ->extraAttributes([
                    'class' => 'real-time-stat cursor-pointer'
                ]),

            Stat::make('Sedang Dipinjam', $peminjamanAktif)
                ->description('Barang sedang dipinjam')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color($peminjamanAktif > 0 ? 'info' : 'gray')
                ->url(route('filament.admin.resources.peminjaman.index', ['tableFilters[status][value]' => 'dipinjam']))
                ->extraAttributes([
                    'class' => 'real-time-stat cursor-pointer'
                ]),

            Stat::make('Peminjaman Hari Ini', $peminjamanHariIni)
                ->description('Total peminjaman baru')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'real-time-stat'
                ]),

            Stat::make('Total User Aktif', $totalUsers)
                ->description($usersHariIni > 0 ? "{$usersHariIni} user baru hari ini" : 'Tidak ada user baru')
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->url(route('filament.admin.resources.users.index'))
                ->extraAttributes([
                    'class' => 'real-time-stat cursor-pointer'
                ]),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total pembayaran yang diterima')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => 'real-time-stat'
                ]),
        ];
    }

    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'isRealTime' => true
        ]);
    }
}
