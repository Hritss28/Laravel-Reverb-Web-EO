@extends('frontend.layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Migration Notice -->
    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-800">
                    Fitur Real-time Tersedia!
                </h3>
                <p class="mt-2 text-blue-700">
                    Halaman ini telah ditingkatkan dengan fitur real-time. Anda akan dialihkan ke versi terbaru yang dapat menampilkan update barang secara langsung saat admin menambah atau mengubah data.
                </p>
                <div class="mt-4">
                    <a href="{{ route('frontend.barang.livewire') }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Gunakan Versi Real-time →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading animation while redirecting -->
    <div class="text-center py-12">
        <div class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memuat versi real-time...
        </div>
    </div>
</div>

<script>
// Auto-redirect after 3 seconds if not already redirected
setTimeout(function() {
    window.location.href = "{{ route('frontend.barang.livewire') }}";
}, 3000);
</script>
@endsection
                    </select>
                </div>

                <!-- Kondisi -->
                <div class="md:w-48">
                    <label for="kondisi" class="block text-sm font-medium text-gray-700">Kondisi</label>
                    <select name="kondisi" id="kondisi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak ringan" {{ request('kondisi') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="perlu perbaikan" {{ request('kondisi') == 'perlu perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </div>

                <!-- Button -->
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        Cari
                    </button>
                    <a href="{{ route('frontend.barang.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Reset
                    </a>
                </div>
            </form>
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Image -->
                    <div class="h-48 bg-gray-200 relative">
                        @if($barang->foto)
                            <img src="{{ Storage::url($barang->foto) }}" alt="{{ $barang->nama }}" class="w-full h-full object-cover">
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
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $barang->kategori->nama }}
                            </span>
                        </div>                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $barang->nama }}</h3>
                        
                        <!-- Price Info -->
                        @if($barang->harga_sewa_per_hari > 0 || $barang->biaya_deposit > 0)
                        <div class="mb-3">
                            @if($barang->harga_sewa_per_hari > 0)
                            <div class="text-lg font-bold text-green-600">{{ $barang->formatted_harga_sewa }}/hari</div>
                            @endif
                            @if($barang->biaya_deposit > 0)
                            <div class="text-sm text-blue-600">Deposit: {{ $barang->formatted_deposit }}</div>
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
@endsection