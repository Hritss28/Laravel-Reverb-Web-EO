<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // Always redirect to Livewire component for better real-time experience
        return redirect()->route('frontend.barang.livewire', $request->all());
    }

    public function show($id)
    {
        try {
            // Ambil data barang berdasarkan ID
            $barang = Barang::findOrFail($id);
            return view('frontend.barang.show', compact('barang'));
        } catch (\Exception $e) {
            // Redirect jika barang tidak ditemukan
            return redirect()->route('frontend.barang.index')
                ->with('error', 'Barang tidak ditemukan');
        }
    }
}