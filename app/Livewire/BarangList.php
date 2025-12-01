<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

#[Layout('frontend.layouts.app')]
class BarangList extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = '';
    public string $kategori = '';
    public string $kondisi = '';
    public int $perPage = 12;

    // Real-time properties
    public array $newBarangIds = [];
    public bool $showNewItemsAlert = false;
    public int $newItemsCount = 0;

    protected $listeners = [
        'echo:barang-updates,barang.created' => 'handleBarangCreated',
        'echo:barang-updates,barang.updated' => 'handleBarangUpdated',
        'echo:barang-updates,barang.deleted' => 'handleBarangDeleted',
        'refresh-barang-list' => 'refresh',
    ];

    public function mount()
    {
        // Get initial filter values from URL
        $this->search = request('search', '');
        $this->kategori = request('kategori', '');
        $this->kondisi = request('kondisi', '');
        
        // Check if there are initial filters from session (from redirect)
        $initialFilters = session('initial_filters', []);
        if (!empty($initialFilters)) {
            $this->search = $initialFilters['search'] ?? '';
            $this->kategori = $initialFilters['kategori'] ?? '';
            $this->kondisi = $initialFilters['kondisi'] ?? '';
            session()->forget('initial_filters');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->updateUrl();
    }

    public function updatedKategori()
    {
        $this->resetPage();
        $this->updateUrl();
    }

    public function updatedKondisi()
    {
        $this->resetPage();
        $this->updateUrl();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->kategori = '';
        $this->kondisi = '';
        $this->resetPage();
        $this->updateUrl();
    }

    private function updateUrl()
    {
        $params = array_filter([
            'search' => $this->search,
            'kategori' => $this->kategori,
            'kondisi' => $this->kondisi,
        ]);

        $this->dispatch('url-updated', $params);
    }

    public function handleBarangCreated($event)
    {
        $barangId = $event['data']['id'] ?? null;
        $barangName = $event['data']['nama'] ?? 'Barang baru';

        if ($barangId && !in_array($barangId, $this->newBarangIds)) {
            $this->newBarangIds[] = $barangId;
            $this->newItemsCount++;
            $this->showNewItemsAlert = true;

            // Show notification
            $this->dispatch('barang-created', [
                'message' => "Barang baru '{$barangName}' telah ditambahkan!",
                'id' => $barangId
            ]);

            // Auto-refresh after 2 seconds if user doesn't interact
            $this->dispatch('auto-refresh-countdown');
        }
    }

    public function handleBarangUpdated($event)
    {
        $barangId = $event['data']['id'] ?? null;
        $barangName = $event['data']['nama'] ?? 'Barang';

        // Refresh the current page to show updates
        $this->dispatch('barang-updated', [
            'message' => "Barang '{$barangName}' telah diperbarui!",
            'id' => $barangId
        ]);

        // Refresh component data
        $this->refresh();
    }

    public function handleBarangDeleted($event)
    {
        $barangName = $event['data']['nama'] ?? 'Barang';

        $this->dispatch('barang-deleted', [
            'message' => "Barang '{$barangName}' telah dihapus!"
        ]);

        // Refresh component data
        $this->refresh();
    }

    public function showNewItems()
    {
        $this->newBarangIds = [];
        $this->newItemsCount = 0;
        $this->showNewItemsAlert = false;
        $this->resetPage();
        $this->refresh();
    }

    public function dismissNewItemsAlert()
    {
        $this->showNewItemsAlert = false;
    }

    public function refresh()
    {
        // Force refresh the component
        $this->dispatch('$refresh');
    }

    private function getBarangQuery()
    {
        $query = Barang::with(['kategori'])
            ->selectRaw("barangs.*, (barangs.stok - COALESCE((SELECT COUNT(*) FROM peminjamans WHERE barang_id = barangs.id AND status = 'dipinjam'), 0)) as stok_tersedia");

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $this->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            });
        }

        // Apply kategori filter
        if ($this->kategori) {
            $query->where('kategori_id', $this->kategori);
        }

        // Apply kondisi filter
        if ($this->kondisi) {
            $query->where('kondisi', $this->kondisi);
        }

        return $query->orderBy('barangs.created_at', 'desc');
    }

    #[Computed]
    public function barangs()
    {
        try {
            return $this->getBarangQuery()->paginate($this->perPage, ['*'], 'page', $this->getPage());
        } catch (\Exception $e) {
            // Log error untuk debugging
            logger('Error in barangs computed: ' . $e->getMessage());
            
            // Return empty paginator
            return new LengthAwarePaginator(
                [], // items
                0,  // total
                $this->perPage, // per page
                $this->getPage(), // current page
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
    }

    #[Computed]
    public function kategoris()
    {
        return Kategori::orderBy('nama')->get();
    }

    public function render()
    {
        return view('livewire.barang-list-fixed', [
            'barangs' => $this->barangs,
            'kategoris' => $this->kategoris,
        ]);
    }
}
