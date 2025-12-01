<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('frontend.layouts.app')]
class DebugBarangList extends Component
{
    public function render()
    {
        return view('livewire.debug-barang-list');
    }
}
