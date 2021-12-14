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
    public function encrypt($data, string $key)
    {
        $chiper = Nullix::encrypt($data, $key);

        return json_decode($chiper, true);
    }

    /**
     * decrypt string.
     */
    public function decrypt($chiper, string $key)
    {
        $data = json_encode($chiper);

        return Nullix::decrypt($data, $key);
    }

    /**
     * validate.
     */
    public function validate(Request $request): bool
    {
        $hasBody = $request->has('ct')
            && $request->has('iv')
            && $request->has('s');

        return $hasBody;
    }
}
