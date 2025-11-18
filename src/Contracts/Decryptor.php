<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

interface Decryptor
{
    /**
     * decrypt string.
     */
    public function decrypt(array $payload, string $passphrase);

    /**
     * validate.
     */
    public function validate(array $data): bool;
}
