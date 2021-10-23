<?php

namespace Denmasyarikin\EncyptResponse;

use Illuminate\Support\Manager as LaravelManager;
use Denmasyarikin\EncyptResponse\Drivers\CryptoJsAes;

class Manager extends LaravelManager
{
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Services\Draft\DriverContract
     */
    public function createCryptojsAesDriver()
    {
        return new CryptoJsAes();
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'cryptojs-aes';
    }
}