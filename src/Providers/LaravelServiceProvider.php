<?php

namespace Denmasyarikin\EncryptResponse\Providers;

use Denmasyarikin\EncryptResponse\Middleware\DecryptRequest;
use Denmasyarikin\EncryptResponse\Middleware\EncryptResponse;
use Illuminate\Contracts\Http\Kernel;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Kernel $kernel)
    {
        if ($this->app->runningInConsole()) {
            $configPath = realpath(__DIR__.'/../config/encrypt_response.php');
            $this->publishes([$configPath => config_path('encrypt_response.php')], 'config');
        }

        if ($this->isServiceEnabled('request')) {
            $kernel->pushMiddleware(DecryptRequest::class);
        }

        if ($this->isServiceEnabled()) {
            $kernel->pushMiddleware(EncryptResponse::class);
        }
    }
}
