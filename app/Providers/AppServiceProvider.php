<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Kategori;
use App\Observers\BarangObserver;
use App\Observers\PeminjamanObserver;
use App\Observers\UserObserver;
use App\Observers\KategoriObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for real-time broadcasting
        Barang::observe(BarangObserver::class);
        Peminjaman::observe(PeminjamanObserver::class);
        User::observe(UserObserver::class);
        Kategori::observe(KategoriObserver::class);
    }
}
