@extends('frontend.layouts.app')

@section('title', 'Pembayaran Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pembayaran Peminjaman</h1>
        <p class="mt-2 text-gray-600">Selesaikan pembayaran untuk konfirmasi peminjaman Anda</p>
    </div>

    <!-- Peminjaman Details -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Peminjaman</h3>
            <p class="text-sm text-gray-500">Kode: {{ $peminjaman->kode_peminjaman }}</p>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Barang Info -->
                <div>
                    <div class="flex items-center space-x-4">
                        @if($peminjaman->barang->foto)
                            <img src="{{ Storage::url($peminjaman->barang->foto) }}" alt="{{ $peminjaman->barang->nama }}" class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $peminjaman->barang->nama }}</h4>
                            <p class="text-sm text-gray-600">{{ $peminjaman->barang->kode_barang }}</p>
                            <p class="text-sm text-gray-600">{{ $peminjaman->barang->kategori->nama }}</p>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Info -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Jumlah:</span>
                        <span class="text-sm font-medium">{{ $peminjaman->jumlah }} unit</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tanggal Pinjam:</span>
                        <span class="text-sm font-medium">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tanggal Kembali:</span>
                        <span class="text-sm font-medium">{{ $peminjaman->tanggal_kembali_rencana->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Durasi:</span>
                        <span class="text-sm font-medium">{{ $peminjaman->durasi + 1 }} hari</span>
                    </div>
                </div>
            </div>

            @if($peminjaman->keperluan)
            <div class="mt-4">
                <h5 class="text-sm font-medium text-gray-700">Keperluan:</h5>
                <p class="text-sm text-gray-600 mt-1">{{ $peminjaman->keperluan }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Ringkasan Pembayaran -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ringkasan Pembayaran</h3>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Biaya Sewa ({{ $peminjaman->durasi + 1 }} hari × {{ $peminjaman->jumlah }} unit):</span>
                    <span class="text-sm font-medium">{{ $peminjaman->formatted_total_biaya_sewa }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Deposit ({{ $peminjaman->jumlah }} unit):</span>
                    <span class="text-sm font-medium">{{ $peminjaman->formatted_total_deposit }}</span>
                </div>
                <hr class="my-3">
                <div class="flex justify-between">
                    <span class="text-base font-semibold text-gray-900">Total Pembayaran:</span>
                    <span class="text-xl font-bold text-blue-600">{{ $peminjaman->formatted_total_pembayaran }}</span>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-50 rounded-md">
                <p class="text-sm text-yellow-800">
                    <strong>Catatan:</strong> Deposit akan dikembalikan setelah barang dikembalikan dalam kondisi baik.
                    Denda keterlambatan 5% per hari dari total biaya sewa.
                </p>
            </div>
        </div>
    </div>

    <!-- Payment Button -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4">
            <div class="text-center">
                <button id="pay-button" class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 text-lg font-medium">
                    <svg class="w-6 h-6 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Bayar Sekarang {{ $peminjaman->formatted_total_pembayaran }}
                </button>
                
                <div class="mt-4">
                    <a href="{{ route('frontend.peminjaman.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                        Kembali ke Daftar Peminjaman
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script type="text/javascript">
document.getElementById('pay-button').onclick = function(){
    // SnapToken acquired from previous step
    snap.pay('{{ $snapToken }}', {
        // Optional
        onSuccess: function(result){
            window.location.href = '{{ route("frontend.peminjaman.payment.finish") }}?order_id={{ $peminjaman->midtrans_order_id }}';
        },
        // Optional
        onPending: function(result){
            alert('Menunggu pembayaran!');
            console.log(result);
        },
        // Optional
        onError: function(result){
            alert('Pembayaran gagal!');
            console.log(result);
            window.location.href = '{{ route("frontend.peminjaman.payment.error") }}';
        }
    });
};
</script>
@endsection
