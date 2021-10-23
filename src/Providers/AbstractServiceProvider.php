<?php

namespace Denmasyarikin\EncyptResponse\Providers;

use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * apply configuration
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
     * check is service enabled
     */
    protected function isServiceEnabled(string $type = 'response'): bool
    {
        if ($type === 'response') {
            return null !== $this->config('response_key') && $this->config('response_enabled') === true;
        }

        if ($type === 'request') {
            return null !== $this->config('request_key') && $this->config('request_enabled') === true;
        }

        return false;
    }

    /**
     * Helper to get the config values.
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return config("encrypt_response.$key", $default);
    }
}
