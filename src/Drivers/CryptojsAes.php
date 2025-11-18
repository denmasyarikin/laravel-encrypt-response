<?php

namespace Denmasyarikin\EncryptResponse\Drivers;

use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use JsonException;

class CryptojsAes implements Encryptor, Decryptor
{
    /**
     * Derive key and IV from passphrase and salt using CryptoJS-compatible OpenSSL method.
     */
    private function deriveKeyAndIv(string $passphrase, string $salt): object
    {
        $keySize = 32; // 256-bit
        $ivSize  = 16; // 128-bit
        $data    = '';
        $d       = '';

        while (strlen($data) < $keySize + $ivSize) {
            $d     = hash('md5', $d . $passphrase . $salt, true);
            $data .= $d;
        }

        return (object) [
            'key' => substr($data, 0, $keySize),
            'iv'  => substr($data, $keySize, $ivSize),
        ];
    }

    /**
     * Encrypt data (compatible with CryptoJS AES + OpenSSL format).
     */
    public function encrypt($data, string $passphrase): array
    {
        $salt = random_bytes(8);

        $derived = $this->deriveKeyAndIv($passphrase, $salt);

        $encrypted = openssl_encrypt(
            is_string($data) ? $data : json_encode($data),
            'aes-256-cbc',
            $derived->key,
            OPENSSL_RAW_DATA,
            $derived->iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed');
        }

        return [
            'ct' => base64_encode($encrypted),
            'iv' => bin2hex($derived->iv),
            's'  => bin2hex($salt),
        ];
    }

    /**
     * Decrypt data encrypted with CryptoJS OpenSSL format.
     */
    public function decrypt(array $payload, string $passphrase)
    {
        if (!$this->validate($payload)) {
            throw new \InvalidArgumentException('Invalid encrypted payload structure');
        }

        $salt = hex2bin($payload['s']);
        $iv   = hex2bin($payload['iv']);
        $ct   = base64_decode($payload['ct'], true);

        if ($salt === false || $iv === false || $ct === false) {
            throw new \InvalidArgumentException('Invalid hex/base64 data in payload');
        }

        $derived = $this->deriveKeyAndIv($passphrase, $salt);

        $decrypted = openssl_decrypt(
            $ct,
            'aes-256-cbc',
            $derived->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Decryption failed: invalid key, data corrupted, or wrong passphrase');
        }

        // Try to decode JSON, fall back to raw string if not valid JSON
        try {
            $json = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
            return $json ?? $decrypted;
        } catch (JsonException) {
            return $decrypted;
        }
    }

    /**
     * Validate payload structure.
     */
    public function validate(array $data): bool
    {
        return isset($data['ct'], $data['iv'], $data['s'])
            && is_string($data['ct'])
            && is_string($data['iv'])
            && is_string($data['s']);
    }
}