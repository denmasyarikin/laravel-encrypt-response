<?php

namespace Denmasyarikin\EncryptResponse\Providers;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Denmasyarikin\EncryptResponse\Manager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();

        if ($this->isServiceEnabled()) {
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
                if ($rDriver !== 'true') {
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
        if (method_exists($this->app, 'configure')) {
            $this->app->configure('encrypt_response');
        }

        $path = realpath(__DIR__.'/../config/encrypt_response.php');
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
     *
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return config("encrypt_response.$key", $default);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Encryptor::class,
            Decryptor::class,
        ];
    }
}
