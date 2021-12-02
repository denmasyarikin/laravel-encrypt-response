<?php

namespace Denmasyarikin\EncryptResponse\Drivers;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Illuminate\Http\Request;
use Nullix\CryptoJsAes\CryptoJsAes as Nullix;

class CryptojsAes implements Decryptor, Encryptor
{
    /**
     * encrypt string.
     */
    public function encrypt(string $data, string $key)
    {
        return Nullix::encrypt($data, $key);
    }

    /**
     * decrypt string.
     */
    public function decrypt(string $plain, string $key)
    {
        return Nullix::decrypt($plain, $key);
    }

    /**
     * validate.
     */
    public function validate(Request $request): bool
    {
        return $request->has('ct')
            && $request->has('iv')
            && $request->has('s');
    }
}
