<?php

namespace Denmasyarikin\EncyptResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nullix\CryptoJsAes\CryptoJsAes;
use RuntimeException;

class DecryptRequest extends BaseMiddleware
{
    const PAYLOAD_KEY = '_payload';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldDecrypt($request)) {
            $payload = $request->input(static::PAYLOAD_KEY);
            $data = $this->decrypt($payload);
            $request->replace($data);
        }

        return $next($request);
    }

    /**
     * let decrypt
     */
    protected function decrypt($payload)
    {
        $password = env('ENCRYPTION_KEY');

        if (!$password) {
            throw new RuntimeException('No password set for encryption');
        }

        return CryptoJsAes::decrypt($payload, $password);
    }

    /**
     * determine response encryption
     */
    protected function shouldDecrypt($request)
    {
        $hasHeader = $request->header('x-encrypt-response') === 'true';
        $hasPayload = $request->has(static::PAYLOAD_KEY);

        return $hasHeader && ($hasPayload || $this->hasJsonHeader($request));
    }

    /**
     * check is response has json header
     */
    protected function hasJsonHeader(Request $request)
    {
        return $request->headers->get('content-type') === 'application/json';
    }
}
