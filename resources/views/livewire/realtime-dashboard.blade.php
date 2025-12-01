<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Realtime Dashboard
                    </h2>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full mr-2 {{ $this->getConnectionStatusColor() === 'text-green-600' ? 'bg-green-400' : ($this->getConnectionStatusColor() === 'text-yellow-600' ? 'bg-yellow-400' : 'bg-red-400') }}"></div>
                                <span class="{{ $this->getConnectionStatusColor() }}">{{ $this->getConnectionStatusText() }}</span>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Last updated: {{ $lastUpdateTime }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
                    <button wire:click="refreshData" 
                            class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                    <button wire:click="testConnection" 
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Test Connection
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="mb-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Barang -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Barang</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ $realtimeStats['total_barang'] ?? 0 }}
                    </dd>
                </div>

                <!-- Stok Rendah -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Stok Rendah</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">
                        {{ $realtimeStats['barang_stok_rendah'] ?? 0 }}
                    </dd>
                </div>

                <!-- Peminjaman Pending -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Pending Approval</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600">
                        {{ $realtimeStats['peminjaman_pending'] ?? 0 }}
                    </dd>
                </div>

                <!-- Peminjaman Aktif -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Sedang Dipinjam</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">
                        {{ $realtimeStats['peminjaman_aktif'] ?? 0 }}
                    </dd>
                </div>

                <!-- Terlambat -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Terlambat</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">
                        {{ $realtimeStats['peminjaman_terlambat'] ?? 0 }}
                    </dd>
                </div>

                <!-- Total Users -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Users</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ $realtimeStats['total_users'] ?? 0 }}
                    </dd>
                </div>

                <!-- Peminjaman Hari Ini -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Peminjaman Hari Ini</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">
                        {{ $realtimeStats['peminjaman_hari_ini'] ?? 0 }}
                    </dd>
                </div>

                <!-- Connection Status -->
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Real-time Status</dt>
                    <dd class="mt-1 text-lg font-semibold tracking-tight {{ $this->getConnectionStatusColor() }}">
                        {{ $this->getConnectionStatusText() }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Activity Feed -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
                        <button wire:click="clearActivity" 
                                class="text-sm text-gray-500 hover:text-gray-700">
                            Clear All
                        </button>
                    </div>
                    <div class="flow-root">
                        <ul class="-mb-8" wire:poll.30s="loadRecentActivity">
                            @forelse($recentActivity as $index => $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                @if($activity['type'] === 'barang') bg-blue-500
                                                @elseif($activity['type'] === 'peminjaman') bg-green-500
                                                @elseif($activity['type'] === 'user') bg-purple-500
                                                @elseif($activity['type'] === 'kategori') bg-yellow-500
                                                @elseif($activity['type'] === 'test') bg-indigo-500
                                                @elseif($activity['type'] === 'system') bg-gray-500
                                                @else bg-gray-400
                                                @endif">
                                                @if($activity['type'] === 'barang')
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v1.5h12V5a2 2 0 00-2-2H4zM2 8.5V14a2 2 0 002 2h8a2 2 0 002-2V8.5H2z" clip-rule="evenodd"></path>
                                                </svg>
                                                @elseif($activity['type'] === 'peminjaman')
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                @elseif($activity['type'] === 'user')
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                </svg>
                                                @elseif($activity['type'] === 'test')
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M13 10V3L4 14h7v7l9-11h-7z" clip-rule="evenodd"></path>
                                                </svg>
                                                @else
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    <span class="font-medium">{{ $activity['title'] }}</span>
                                                </p>
                                                <p class="text-sm text-gray-500">{{ $activity['message'] }}</p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="mt-2">No recent activity</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">System Information</h3>
                    
                    <div class="space-y-4">
                        <!-- Connection Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">WebSocket Status</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($connectionStatus === 2) bg-green-100 text-green-800
                                @elseif($connectionStatus === 1) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $this->getConnectionStatusText() }}
                            </span>
                        </div>

                        <!-- Last Update -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Last Update</span>
                            <span class="text-sm text-gray-900">{{ $lastUpdateTime }}</span>
                        </div>

                        <!-- Auto Refresh -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Auto Refresh</span>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model.live="autoRefresh" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Enabled</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="space-y-2">
                                <button wire:click="testConnection" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Send Test Event
                                </button>
                                
                                <button wire:click="refreshData" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh Data
                                </button>
                                
                                <a href="{{ route('filament.admin.pages.dashboard') }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                    Go to Admin Panel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh when enabled -->
    @if($autoRefresh)
    <div wire:poll.30s="loadRealtimeStats"></div>
    @endif
</div>

@script
<script>
    // Handle notifications
    $wire.on('show-notification', (event) => {
        const data = event[0] || event;
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50`;
        
        let bgColor = 'bg-blue-50';
        let iconColor = 'text-blue-400';
        let textColor = 'text-blue-800';
        
        if (data.type === 'success') {
            bgColor = 'bg-green-50';
            iconColor = 'text-green-400';
            textColor = 'text-green-800';
        } else if (data.type === 'danger' || data.type === 'error') {
            bgColor = 'bg-red-50';
            iconColor = 'text-red-400';
            textColor = 'text-red-800';
        } else if (data.type === 'warning') {
            bgColor = 'bg-yellow-50';
            iconColor = 'text-yellow-400';
            textColor = 'text-yellow-800';
        }
        
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">${data.title}</p>
                        <p class="mt-1 text-sm text-gray-500">${data.message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    });

    // Handle stats refresh
    $wire.on('stats-refreshed', (event) => {
        console.log('Stats refreshed:', event);
        // Could add visual feedback here
    });
</script>
@endscript
