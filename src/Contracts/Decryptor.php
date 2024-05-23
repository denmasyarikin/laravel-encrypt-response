<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

interface Decryptor
{
    /**
     * decrypt string.
     */
    public function decrypt($chiper, string $password);

    /**
     * validate.
     */
    public function validate(array $data): bool;
}
