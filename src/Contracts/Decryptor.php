<?php

namespace Denmasyarikin\EncyptResponse\Contracts;

interface Decryptor
{
    /**
     * decrypt string
     */
    public function decrypt(string $plain, string $password);
}