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
            return new App\Services\Social\ServiceFacebook($app['App\Repositories\Social\RepositoryFacebook']);
        });

        $this->app->singleton('ServiceTwitter', function ($app) {
            return new App\Services\Social\ServiceTwitter($app['App\Repositories\Social\RepositoryTwitter']);
        });

        // Binding service factory instance
        $socialServiceFactory = new App\Services\SocialServiceFactory();
        $this->app->instance('SocialServiceFactory', $socialServiceFactory);
    }

    public function provides()
    {
        return [App\Repositories\Social\RepositoryFacebook::class, App\Repositories\Social\RepositoryTwitter::class, 
            App\Services\Social\ServiceFacebook::class, App\Services\Social\ServiceTwitter::class];
    }
}
