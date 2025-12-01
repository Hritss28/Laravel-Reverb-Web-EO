<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Livewire\BarangList;
use Illuminate\Http\Request;

class BarangLivewireController extends Controller
{
    public function index()
    {
        // Create the Livewire component
        $component = new BarangList();
        $component->mount();
        
        // Get the data
        $barangs = $component->getBarangsProperty();
        $kategoris = $component->getKategorisProperty();
        
        // Return the view with data
        return view('frontend.barang.livewire-test', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'search' => $component->search,
            'kategori' => $component->kategori,
            'kondisi' => $component->kondisi,
            'showNewItemsAlert' => $component->showNewItemsAlert,
            'newItemsCount' => $component->newItemsCount,
            'newBarangIds' => $component->newBarangIds,
        ]);
    }
}
