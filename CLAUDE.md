# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel package (`denmasyarikin/laravel-encrypt-response`) providing two middlewares: one that encrypts outgoing JSON responses and one that decrypts encrypted incoming request bodies. The default driver implements the CryptoJS AES + OpenSSL on-the-wire format so JS clients using CryptoJS can interop directly.

## Commands

- `composer install` — install dependencies.
- `composer fix` — run `php-cs-fixer fix . --rules=@Symfony` over the whole tree. This is the only declared script and the de-facto lint/format step.

There is no test suite, no build step, and no separate lint config beyond the `@Symfony` rule set used by `composer fix`.

## Architecture

The package wires itself in via Laravel auto-discovery (`extra.laravel.providers` in `composer.json`) — there is no manual registration.

### Boot flow (`src/ServiceProvider.php`)

1. `register()` merges `src/config.php` under the `encrypt_response` config key, then conditionally binds singletons for `Contracts\Encryptor` and `Contracts\Decryptor`. Each binding is gated by `isServiceEnabled('response' | 'request')`, which requires BOTH the corresponding key (`response_key` / `request_key`) AND the enabled flag.
2. The encryptor/decryptor binding resolves a driver name dynamically from the **current request's header** (`response_header_key` / `request_header_key`, default `x-encrypt-response` / `x-encrypt-request`), falling back to `response_driver` from config. The value `"true"` is treated as "use the configured default" rather than a driver name. This means the bound implementation can differ per request.
3. `boot()` pushes `DecryptRequest` and/or `EncryptResponse` onto the global HTTP kernel middleware stack via `$kernel->pushMiddleware(...)`, again gated by `isServiceEnabled`. Consumers do not register middleware themselves.

### Driver selection (`src/Manager.php`)

Extends Laravel's `Illuminate\Support\Manager`. The default driver is `cryptojs-aes`, created by `createCryptojsAesDriver()`. To add a new driver: implement `Contracts\Encryptor` and/or `Contracts\Decryptor`, then add a `create<Name>Driver()` method on `Manager`. The driver key is studly-cased by Laravel's Manager (`cryptojs-aes` → `createCryptojsAesDriver`).

### Middlewares (`src/Middleware/`)

- `BaseMiddleware` loads the full `encrypt_response` config into `$this->config` in its constructor and provides `isServiceEnabled()` plus `inExceptArray()` (matches against `route_except` using both `fullUrlIs` and `is`).
- `EncryptResponse` runs after the response is generated. It re-wraps the response as a new `JsonResponse` whose body is the encryptor output, copies original headers, and adds the `response_header_key` header set to the driver name so clients know which driver to use. Encrypts only when (a) service enabled, (b) either `response_optional` is false OR the request sent the response header (opt-in mode), and (c) the response is a `JsonResponse` or has `content-type: application/json`. Exceptions during encryption return a 500 with an encrypted `{message}` payload.
- `DecryptRequest` runs before the controller. Only acts on `POST`/`PUT` requests with a non-empty body when the decryptor is bound. Calls `decryptor->validate($request->all())` first and throws `BadRequestHttpException('Payload data is not encrypted')` if the shape is wrong; otherwise replaces the request body with the decrypted data via `$request->replace()`.

### CryptoJS interop (`src/Drivers/CryptojsAes.php`)

Implements both contracts in one class. Key/IV are derived from passphrase + 8-byte salt using the OpenSSL EVP_BytesToKey MD5 schedule that CryptoJS uses (32-byte key, 16-byte IV, AES-256-CBC). Payload wire format is `{ ct: base64, iv: hex, s: hex }`. `decrypt()` attempts JSON-decode of the plaintext and falls back to the raw string if it isn't valid JSON. `validate()` is the structural check used by `DecryptRequest` before attempting decryption.

## Configuration notes

`src/config.php` defines defaults. Keys default to `APP_KEY` if their dedicated env vars are unset. `route_except` is an array of route patterns (Laravel `request->is()` syntax) that bypass both middlewares. `response_optional=true` flips encryption to opt-in per request (client must send the response header).
