<?php

namespace Denmasyarikin\EncryptResponse\Drivers;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
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
    public function validate(array $data): bool
    {
        $hasCt = isset($data['ct']);
        $hasIv = isset($data['iv']);
        $hasSalt = isset($data['s']);

        return $hasCt && $hasIv && $hasSalt;
    }
}
