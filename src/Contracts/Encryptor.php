<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

interface Encryptor
{
    /**
     * encrypt string.
     */
    public function encrypt($data, string $passphrase);
}
