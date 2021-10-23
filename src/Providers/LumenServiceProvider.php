<?php

namespace Denmasyarikin\EncyptResponse\Providers;

use Denmasyarikin\EncyptResponse\Middleware\EncryptResponse;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $configPath = realpath(__DIR__.'/../config/encrypt_response.php');
            $this->publishes([$configPath => config_path('encrypt_response.php')], 'config');
        }

        if ($this->isServiceEnabled()) {
            $this->app->middleware(EncryptResponse::class);
        }
    }
}
