<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
// Tambahkan import ini di atas
use Illuminate\Support\ServiceProvider; // Tambahkan import ini di atas

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
        FilamentAsset::register([
            \Filament\Support\Assets\Js::make('livewire-custom', asset('vendor/livewire/livewire.js')),
        ]);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
