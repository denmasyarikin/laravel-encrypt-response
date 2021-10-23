<?php

namespace Denmasyarikin\EncyptResponse\Contracts;

interface Encryptor
{
    /**
     * encrypt string
     */
    public function encrypt(string $data, string $password);
}