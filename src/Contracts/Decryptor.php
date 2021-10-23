<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

interface Decryptor
{
    /**
     * decrypt string
     */
    public function decrypt(string $plain, string $password);
}