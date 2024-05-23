<?php

namespace Denmasyarikin\EncryptResponse\Middleware;

use Illuminate\Http\Request;

class BaseMiddleware
{
    /**
     * configuration.
     *
     * @var array
     */
    protected $config = [];

    public function __construct()
    {
        $this->config = config('encrypt_response');
    }

    /**
     * check is service enabled.
     */
    protected function isServiceEnabled(string $type = 'response'): bool
    {
        if ('response' === $type) {
            return null !== $this->config['response_key'] && true === $this->config['response_enabled'];
        }

        if ('request' === $type) {
            return null !== $this->config['request_key'] && true === $this->config['request_enabled'];
        }

        return false;
    }

    /**
     * Determine if the request has a URI that should be accessible in maintenance mode.
     *
     * @return bool
     */
    protected function inExceptArray(Request $request)
    {
        foreach ($this->config['route_except'] as $except) {
            if ('/' !== $except) {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
