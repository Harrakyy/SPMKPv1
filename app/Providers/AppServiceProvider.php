<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Permintaan;
use App\Observers\RequestObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Permintaan::observe(RequestObserver::class);
    }
}