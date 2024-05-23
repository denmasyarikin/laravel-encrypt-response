<?php

namespace Denmasyarikin\EncryptResponse;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Denmasyarikin\EncryptResponse\Middleware\DecryptRequest;
use Denmasyarikin\EncryptResponse\Middleware\EncryptResponse;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();

        if ($this->isServiceEnabled('response')) {
            $this->createEncryptor();
        }

        if ($this->isServiceEnabled('request')) {
            $this->createDecryptor();
        }
    }

    /**
     * create encryptor.
     */
    protected function createEncryptor()
    {
        $this->app->singleton(Encryptor::class, function ($app) {
            $request = $app->make('request');
            $driver = $this->config('response_driver');

            if ($request->headers->has($this->config('response_header_key'))) {
                $rDriver = $request->header($this->config('response_header_key'));
                if ('true' !== $rDriver) {
                    $driver = $rDriver;
                }
            }

            return (new Manager($app))->driver($driver);
        });
    }

    /**
     * create decryptor.
     */
    protected function createDecryptor()
    {
        $this->app->singleton(Decryptor::class, function ($app) {
            $request = $app->make('request');

            if ($request->headers->has($this->config('request_header_key'))) {
                try {
                    $driver = $request->header($this->config('request_header_key'));

                    return (new Manager($app))->driver($driver);
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e->getMessage());
                }
            }

            return null;
        });
    }

    /**
     * apply configuration.
     */
    protected function configure()
    {
        $path = realpath(__DIR__.'/config.php');

        $this->mergeConfigFrom($path, 'encrypt_response');
    }

    /**
     * check is service enabled.
     */
    protected function isServiceEnabled(string $type = 'response'): bool
    {
        if ('response' === $type) {
            return null !== $this->config('response_key') && true === $this->config('response_enabled');
        }

        if ('request' === $type) {
            return null !== $this->config('request_key') && true === $this->config('request_enabled');
        }

        return false;
    }

    /**
     * Helper to get the config values.
     *
     * @param string $key
     * @param string $default
     */
    protected function config($key, $default = null)
    {
        return config("encrypt_response.{$key}", $default);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(Kernel $kernel): void
    {
        if ($this->app->runningInConsole()) {
            $configPath = realpath(__DIR__.'/config.php');
            $this->publishes([$configPath => config_path('encrypt_response.php')], 'config');
        }

        if ($kernel instanceof HttpKernel) {
            if ($this->isServiceEnabled('request')) {
                $kernel->pushMiddleware(DecryptRequest::class);
            }
    
            if ($this->isServiceEnabled()) {
                $kernel->pushMiddleware(EncryptResponse::class);
            }
        }
    }
}
