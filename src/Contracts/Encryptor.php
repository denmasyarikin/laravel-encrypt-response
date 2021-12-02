<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

interface Encryptor
{
    /**
     * encrypt string.
     */
    public function encrypt(string $data, string $password);
}
