<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\BarangController;
use App\Http\Controllers\Frontend\PeminjamanController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ChatController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Middleware\DomainMiddleware;
use Illuminate\Support\Facades\Route;

// Standalone Realtime Dashboard (Livewire component)
Route::get('/realtime-dashboard', \App\Livewire\RealtimeDashboard::class)
    ->middleware(['auth'])
    ->name('realtime.dashboard');

// Test routes for Livewire Realtime Dashboard
Route::get('/test-livewire-dashboard', function () {
    // Create some test data updates
    $barang = \App\Models\Barang::first();
    if ($barang) {
        $barang->update(['updated_at' => now()]);
    }
    
    $peminjaman = \App\Models\Peminjaman::first();
    if ($peminjaman) {
        $peminjaman->update(['updated_at' => now()]);
    }
    
    // Send test broadcast
    broadcast(new \App\Events\TestEvent([
        'message' => 'Livewire Dashboard Test at ' . now()->format('H:i:s'),
        'timestamp' => now(),
        'source' => 'test-route',
        'user' => \Illuminate\Support\Facades\Auth::user()?->name ?? 'Anonymous'
    ]));
    
    return response()->json([
        'status' => 'success',
        'message' => 'Test events sent to Livewire Dashboard',
        'timestamp' => now()
    ]);
})->middleware('web')->name('test.livewire.dashboard');

// Test broadcast route for debugging
Route::post('/test-broadcast', function () {
    broadcast(new \App\Events\TestEvent([
        'message' => 'Test broadcast from API',
        'timestamp' => now(),
        'user' => \Illuminate\Support\Facades\Auth::user()?->name ?? 'Anonymous'
    ]));
    
    return response()->json(['status' => 'Test event broadcasted']);
})->middleware('web');

// Test peminjaman update
Route::get('/test-peminjaman-update', function () {
    $peminjaman = \App\Models\Peminjaman::first();
    if ($peminjaman) {
        // Trigger update to test real-time
        $peminjaman->touch();
        
        return response()->json([
            'status' => 'Peminjaman updated',
            'id' => $peminjaman->id,
            'kode' => $peminjaman->kode_peminjaman
        ]);
    }
    
    return response()->json(['error' => 'No peminjaman found']);
});

// Debug route to test Livewire
Route::get('/debug-barang', \App\Livewire\DebugBarangList::class)->name('debug.barang');

// Apply domain middleware to all routes
Route::middleware([DomainMiddleware::class])->group(function () {
    
    // User Domain Routes (user.inventaris.local)
    Route::domain('app.localhost')->group(function () {
        Route::get('/', function () {
            return view('user.home');
        });
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::post('/frontend/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('frontend.logout');

        Route::middleware('guest')->group(function () {
            // Route register sudah ada di auth.php, tidak perlu duplikasi
        });

        // Public test routes (no auth required)
        Route::get('/test-barang-public', \App\Livewire\BarangList::class)->name('test.barang.public');
        Route::get('/test-simple', \App\Livewire\TestSimple::class)->name('test.simple');
        Route::get('/test-barang-livewire', function() {
            return view('test-barang-livewire');
        })->name('test.barang.livewire');

        // Public routes untuk testing
        Route::get('/frontend/barang-livewire', \App\Livewire\BarangList::class)->name('frontend.barang.livewire');
        Route::get('/frontend/barang-test', [App\Http\Controllers\Frontend\BarangLivewireController::class, 'index'])->name('frontend.barang.test');

        // Route Frontend (User only)
        Route::middleware(['auth'])->group(function () {
            // Dashboard
            Route::get('/frontend/dashboard', [DashboardController::class, 'index'])->name('frontend.dashboard');
            
            // Barang Routes
            Route::get('/frontend/barang', [BarangController::class, 'index'])->name('frontend.barang.index');
            Route::get('/frontend/barang/{id}', [BarangController::class, 'show'])->name('frontend.barang.show');
            
            // Peminjaman Routes
            Route::get('/frontend/peminjaman', [PeminjamanController::class, 'index'])->name('frontend.peminjaman.index');
            Route::get('/frontend/peminjaman/create', [PeminjamanController::class, 'create'])->name('frontend.peminjaman.create');
            Route::post('/frontend/peminjaman', [PeminjamanController::class, 'store'])->name('frontend.peminjaman.store');
            Route::get('/frontend/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('frontend.peminjaman.show');
            Route::post('/frontend/peminjaman/get-barang-details', [PeminjamanController::class, 'getBarangDetails'])->name('frontend.peminjaman.get-barang-details');
            Route::get('/frontend/peminjaman/{id}/payment', [PeminjamanController::class, 'payment'])->name('frontend.peminjaman.payment');
            Route::get('/frontend/peminjaman/payment/finish', [PeminjamanController::class, 'paymentFinish'])->name('frontend.peminjaman.payment.finish');
            Route::get('/frontend/peminjaman/payment/unfinish', [PeminjamanController::class, 'paymentUnfinish'])->name('frontend.peminjaman.payment.unfinish');
            Route::get('/frontend/peminjaman/payment/error', [PeminjamanController::class, 'paymentError'])->name('frontend.peminjaman.payment.error');
            
            // Chat Routes
            Route::get('/frontend/chat', [ChatController::class, 'index'])->name('frontend.chat.index');
        });
    });

    // Admin Domain Routes (admin.localhost)
    Route::domain('admin.localhost')->group(function () {
        Route::get('/', function () {
            return redirect('/admin');
        });

        // Filament admin routes
        Route::get('/admin/login', function () {
            return redirect('/admin/login');
        });
    });

    // Fallback routes for localhost development
    Route::get('/', function () {
        $domain = request()->getHost();
        if (str_contains($domain, '8080') || session('domain_type') === 'admin') {
            return redirect('/admin');
        }
        return app(HomeController::class)->index();
    })->name('home.fallback');
});

// Webhook Routes (No Authentication Required)
// Midtrans Payment Callback - must be accessible without auth
Route::post('/frontend/peminjaman/payment/callback', [PeminjamanController::class, 'paymentCallback'])
    ->name('frontend.peminjaman.payment.callback')
    ->withoutMiddleware(['auth', 'verified']);

require __DIR__.'/auth.php';