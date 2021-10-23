<?php

namespace Denmasyarikin\EncryptResponse\Providers;

use Denmasyarikin\EncryptResponse\Middleware\DecryptRequest;
use Denmasyarikin\EncryptResponse\Middleware\EncryptResponse;

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
