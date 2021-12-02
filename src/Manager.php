<?php

namespace Denmasyarikin\EncryptResponse;

use Denmasyarikin\EncryptResponse\Drivers\CryptojsAes;
use Illuminate\Support\Manager as LaravelManager;

class Manager extends LaravelManager
{
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Services\Draft\DriverContract
     */
    public function createCryptojsAesDriver()
    {
        return new CryptojsAes();
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
