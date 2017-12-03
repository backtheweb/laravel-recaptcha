<?php

namespace Backtheweb\ReCaptcha;

use Illuminate\Support\ServiceProvider;

class ReCaptchaServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $app = $this->app;

        $this->publishes([__DIR__.'/../config/reCaptcha.php' => config_path('reCaptcha.php')], 'config');

        $app['validator']->extend('captcha', function ($attribute, $value) use ($app) {

            return $app['ReCaptcha']->verifyResponse($value, $app['request']->getClientIp());
        });

    }

    /**
     * Register the service provider.
     */
    public function register()
    {

        $this->app->singleton('ReCaptcha', function ($app) {

            $config = $app['config']['reCaptcha'];

            return new ReCaptcha($config['secret'], $config['key'], $config['attributes']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }

}
