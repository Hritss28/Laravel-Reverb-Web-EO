<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Barang</h1>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Cari nama barang, kode, atau lokasi..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select wire:model.live="kategori" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kondisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                    <select wire:model.live="kondisi" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak ringan">Rusak Ringan</option>
                        <option value="perlu perbaikan">Perlu Perbaikan</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button wire:click="resetFilters" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:ring-2 focus:ring-gray-500">
                        Reset
                    </button>
                    <button wire:click="refresh" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="mb-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
            <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-800">Memuat data...</span>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            Menampilkan {{ $barangs->firstItem() ?? 0 }} - {{ $barangs->lastItem() ?? 0 }} dari {{ $barangs->total() }} barang
        </p>
    </div>

    <!-- Barang Grid -->
    @if($barangs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($barangs as $barang)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow {{ in_array($barang->id, $newBarangIds) ? 'ring-2 ring-blue-300 animate-pulse' : '' }}">
                    <!-- Image -->
                    <div class="h-48 bg-gray-200 relative">
                        @if($barang->foto)
                            <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $barang->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $barang->stok_tersedia > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>

                        <!-- New Badge -->
                        @if(in_array($barang->id, $newBarangIds))
                        <div class="absolute top-2 left-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-bounce">
                                Baru!
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $barang->kategori->nama }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $barang->nama }}</h3>
                        
                        <!-- Price Info -->
                        @if($barang->harga_sewa_per_hari > 0 || $barang->biaya_deposit > 0)
                        <div class="mb-3">
                            @if($barang->harga_sewa_per_hari > 0)
                            <div class="text-lg font-bold text-green-600">Rp {{ number_format($barang->harga_sewa_per_hari, 0, ',', '.') }}/hari</div>
                            @endif
                            @if($barang->biaya_deposit > 0)
                            <div class="text-sm text-blue-600">Deposit: Rp {{ number_format($barang->biaya_deposit, 0, ',', '.') }}</div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Code -->
                        <p class="text-sm text-gray-500 mb-2">{{ $barang->kode_barang }}</p>

                        <!-- Stock Info -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-600">Stok: {{ $barang->stok_tersedia }}/{{ $barang->stok }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $barang->kondisi == 'baik' ? 'bg-green-100 text-green-800' : 
                                   ($barang->kondisi == 'rusak ringan' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($barang->kondisi) }}
                            </span>
                        </div>

                        <!-- Location -->
                        <p class="text-sm text-gray-600 mb-3">📍 {{ $barang->lokasi }}</p>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('frontend.barang.show', $barang) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded-md hover:bg-blue-700 text-sm">
                                Lihat Detail
                            </a>
                            @auth
                                @if($barang->stok_tersedia > 0)
                                    <a href="{{ route('frontend.peminjaman.create', ['barang_id' => $barang->id]) }}" 
                                       class="bg-green-600 text-white py-2 px-3 rounded-md hover:bg-green-700 text-sm">
                                        Pinjam
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $barangs->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada barang ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500">Coba ubah filter pencarian Anda.</p>
        </div>
    @endif
</div>
