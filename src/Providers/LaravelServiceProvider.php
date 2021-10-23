<?php

namespace Denmasyarikin\EncyptResponse\Providers;

use Denmasyarikin\EncyptResponse\Middleware\DecryptRequest;
use Denmasyarikin\EncyptResponse\Middleware\EncryptResponse;
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

        if ($this->isServiceEnabled()) {
            $kernel->prependMiddleware(EncryptResponse::class);
        }

        if ($this->isServiceEnabled('request')) {
            $kernel->prependMiddleware(DecryptRequest::class);
        }
    }
}
