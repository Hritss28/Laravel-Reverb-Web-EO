<div>
    <h1>Test Livewire Barang List</h1>
    
    <div class="mb-4">
        <p>Search: {{ $search }}</p>
        <p>Kategori: {{ $kategori }}</p>
        <p>Kondisi: {{ $kondisi }}</p>
    </div>
    
    <div class="mb-4">
        <p>Total Barangs: {{ $barangs->total() }}</p>
        <p>Current Page: {{ $barangs->currentPage() }}</p>
        <p>Items on this page: {{ $barangs->count() }}</p>
    </div>
    
    @if($barangs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($barangs as $barang)
                <div class="border p-4 rounded">
                    <h3 class="font-bold">{{ $barang->nama }}</h3>
                    <p>Kode: {{ $barang->kode_barang }}</p>
                    <p>Stok: {{ $barang->stok }}</p>
                    <p>Kategori: {{ $barang->kategori->nama ?? 'N/A' }}</p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $barangs->links() }}
        </div>
    @else
        <p>Tidak ada barang ditemukan.</p>
    @endif
</div>
