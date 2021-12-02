<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

use Illuminate\Http\Request;

interface Decryptor
{
    /**
     * decrypt string.
     */
    public function decrypt(string $plain, string $password);

    /**
     * validate.
     */
    public function validate(Request $request): bool;
}
