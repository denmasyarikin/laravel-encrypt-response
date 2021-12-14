<?php

namespace Denmasyarikin\EncryptResponse\Contracts;

use Illuminate\Http\Request;

interface Decryptor
{
    /**
     * decrypt string.
     */
    public function decrypt($chiper, string $password);

    /**
     * validate.
     */
    public function validate(Request $request): bool;
}
