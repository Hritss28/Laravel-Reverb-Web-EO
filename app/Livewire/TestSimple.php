<?php

namespace App\Livewire;

use Livewire\Component;

class TestSimple extends Component
{
    public $message = "Hello Livewire!";
    
    public function render()
    {
        return '<div>
            <h1>Test Simple Component</h1>
            <p>{{ $message }}</p>
            <p>Current time: ' . now() . '</p>
            <button wire:click="updateMessage">Update Message</button>
        </div>';
    }
    
    public function updateMessage()
    {
        $this->message = "Updated at " . now();
    }
}
