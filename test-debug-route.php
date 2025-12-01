<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set up request context
$_SERVER['HTTP_HOST'] = 'app.localhost';
$_SERVER['REQUEST_URI'] = '/frontend/barang-livewire';

try {
    echo "=== Testing Livewire Route ===" . PHP_EOL;
    
    // Create a mock request
    $request = \Illuminate\Http\Request::create('/frontend/barang-livewire', 'GET');
    $request->server->set('HTTP_HOST', 'app.localhost');
    
    // Create component
    $component = new \App\Livewire\BarangList();
    $component->mount();
    
    echo "✅ Component mounted successfully" . PHP_EOL;
    
    // Test property getters
    $barangs = $component->getBarangsProperty();
    $kategoris = $component->getKategorisProperty();
    
    echo "Barangs: " . $barangs->count() . " items" . PHP_EOL;
    echo "Kategoris: " . $kategoris->count() . " items" . PHP_EOL;
    
    // Test rendering
    $view = $component->render();
    echo "✅ View rendered successfully" . PHP_EOL;
    echo "View name: " . $view->name() . PHP_EOL;
    
    // Test the actual view file exists
    $viewPath = resource_path('views/' . str_replace('.', '/', $view->name()) . '.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ View file exists: " . $viewPath . PHP_EOL;
    } else {
        echo "❌ View file NOT found: " . $viewPath . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
