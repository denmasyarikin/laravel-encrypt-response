<?php

namespace Denmasyarikin\EncryptResponse\Middleware;

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
}
