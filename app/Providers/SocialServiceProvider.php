<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Binding singleton services
        $this->app->singleton('ServiceFacebook', function ($app) {
            return new App\Services\Social\ServiceFacebook($app['App\Repositories\ProviderRepository']);
        });

        $this->app->singleton('ServiceTwitter', function ($app) {
            return new App\Services\Social\ServiceTwitter($app['App\Repositories\ProviderRepository']);
        });

        $this->app->singleton('ServiceGoogle', function ($app) {
            return new App\Services\Social\ServiceGoogle($app['App\Repositories\ProviderRepository']);
        });

        $this->app->singleton('ServiceLinkedin', function ($app) {
            return new App\Services\Social\ServiceLinkedin($app['App\Repositories\ProviderRepository']);
        });

        // Binding service factory instance
        $socialServiceFactory = new App\Services\SocialServiceFactory();
        $this->app->instance('SocialServiceFactory', $socialServiceFactory);
    }

    public function provides()
    {
        return [App\Services\Social\ServiceFacebook::class, App\Services\Social\ServiceTwitter::class, App\Services\Social\ServiceGoogle::class];
    }
}
