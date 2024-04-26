<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sheet;
use App\Observers\SheetObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sheet::observe(SheetObserver::class);
    }
}
