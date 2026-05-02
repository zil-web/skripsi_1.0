<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Transaksi;

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
        // Share global data used by the admin layout (pending transaction count)
        try {
            $pending_count = Transaksi::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            // In case migrations haven't run or DB not configured yet, fallback to 0
            $pending_count = 0;
        }

        View::share('pending_count', $pending_count);
    }
}
