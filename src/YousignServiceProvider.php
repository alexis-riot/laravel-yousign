<?php

namespace AlexisRiot\Yousign;

use Illuminate\Support\ServiceProvider;

class YousignServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/yousign.php' => config_path('yousign.php'),
        ], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'yousign');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/yousign'),
        ], 'translation');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/yousign.php', 'yousign'
        );

        $this->app->bind('yousign', function ($app) {
            return new Yousign($app);
        });

        $this->app->alias('yousign', Yousign::class);
    }
}
