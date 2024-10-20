<?php

namespace Denmasyarikin\EncryptResponse\Drivers;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Blocktrail\CryptoJSAES\CryptoJSAES as Blocktrail;

class CryptojsAes implements Decryptor, Encryptor
{
    /**
     * encrypt string.
     */
    public function encrypt($data, string $key)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $key . $salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return [
            'ct' => base64_encode($encrypted_data),
            'iv' => bin2hex($iv),
            's' => bin2hex($salt)
        ];
    }

    /**
     * decrypt string.
     */
    public function decrypt($json, string $key)
    {
        $salt = hex2bin($json["s"]);
        $iv = hex2bin($json["iv"]);
        $ct = base64_decode($json["ct"]);
        $concatedPassphrase = $key . $salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        $i = 1;

        while (strlen($result) < 32) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
            $i++;
        }

        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return json_decode($data, true);
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
