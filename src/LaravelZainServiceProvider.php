<?php

namespace AlkhatibDev\LaravelZain;

use Illuminate\Support\ServiceProvider;

class LaravelZainServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('Zain', function ($app) {
            return new \AlkhatibDev\LaravelZain\Zain();
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishResources();
    }

    protected function publishResources()
    {
        // Publish Configs
        $this->publishes([
            __DIR__.'/../config/laravel-zain.php' => config_path('laravel-zain.php'),
        ], 'laravel-zain-config');
    }

}
