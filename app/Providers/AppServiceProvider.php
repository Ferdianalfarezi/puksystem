<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PengajuanBudget; // ✅ PERBAIKI INI - Model bukan Provider
use App\Observers\PengajuanBudgetObserver;

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
        // ✅ Register Observer
        PengajuanBudget::observe(PengajuanBudgetObserver::class);
    }
}