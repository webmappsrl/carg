<?php

namespace App\Providers;

use App\Nova\ConfFeatureCollection;
use App\Nova\FeatureCollection;
use App\Nova\Media;
use App\Nova\Sheet;
use App\Nova\UgcPoi;
use App\Nova\UgcTrack;
use App\Nova\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Wm\WmPackage\Nova\App;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Nova::withBreadcrumbs(true);
        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::make(' ', [
                    MenuItem::resource(App::class)->canSee(function (Request $request) {
                        return $request->user()->email === 'admin@webmapp.it';
                    }),
                    MenuItem::resource(User::class),
                    MenuItem::resource(Media::class),
                ])->icon(''),
                MenuSection::make('Settings', [
                    MenuItem::resource(ConfFeatureCollection::class),
                ])->icon('adjustments')->collapsable(),
                MenuSection::make('Data', [
                    MenuItem::resource(Sheet::class),
                    MenuItem::resource(FeatureCollection::class),
                ])->icon('database')->collapsable(),
                MenuSection::make('UGC', [
                    MenuItem::resource(UgcPoi::class),
                    MenuItem::resource(UgcTrack::class),
                ])->icon('users')->collapsable(),
                MenuSection::make('Tools', [
                    MenuItem::externalLink('Horizon', url('/horizon'))->openInNewTab(),
                    MenuItem::externalLink('Telescope', url('/telescope'))->openInNewTab(),
                ])->icon('briefcase')->canSee(function (Request $request) {
                    return $request->user()->email === 'admin@webmapp.it';
                }),
            ];
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'admin@webmapp.it',
                'roberta.carta@isprambiente.it',
                'team@webmapp.it',
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
}
