<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentRealtimeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register JavaScript assets for real-time functionality
        FilamentAsset::register([
            Js::make('echo', resource_path('js/echo.js'))->loadedOnRequest(),
            Js::make('filament-simple', resource_path('js/filament-simple.js'))->loadedOnRequest(),
        ]);
    }
}
