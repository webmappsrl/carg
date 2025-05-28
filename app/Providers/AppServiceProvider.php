<?php

namespace App\Providers;

use App\Models\Sheet;
use App\Observers\SheetObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\AppFeatureCollectionConfigObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sheet::observe(SheetObserver::class);
        App::observe(AppFeatureCollectionConfigObserver::class);
    }
}
