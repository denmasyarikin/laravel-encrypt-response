<?php

namespace Denmasyarikin\EncyptResponse\Providers;

use Denmasyarikin\EncyptResponse\Middleware\DecryptRequest;
use Denmasyarikin\EncyptResponse\Middleware\EncryptResponse;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->isServiceEnabled()) {
            $this->app->middleware(EncryptResponse::class);
        }

        if ($this->isServiceEnabled('request')) {
            $this->app->middleware(DecryptRequest::class);
        }
    }
}
